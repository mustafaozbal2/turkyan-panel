from fastapi import FastAPI
from pydantic import BaseModel
import base64, io, time, threading, requests, cv2
from PIL import Image
import numpy as np
from ultralytics import YOLO

app = FastAPI(title="FireAI")
# Model dosyası: ai/models/fire_smoke_best.pt (yoksa yolun altındaki "yolo11n.pt" ile başlar)
try:
    model = YOLO("models/fire_smoke_best.pt")
except Exception:
    model = YOLO("yolo11n.pt")  # internet yoksa ilk koşuda indiremeyebilir; local koyman daha iyi

class ImageReq(BaseModel):
    image_base64: str
    meta: dict | None = None

class StreamCfg(BaseModel):
    stream_url: str  # rtsp://... veya 0 (USB kamera)
    webhook_url: str
    min_frames: int = 10
    prob_threshold: float = 0.7
    window_seconds: int = 5

def infer_np(img_np, conf=0.25, imgsz=640):
    # Ultralytics sonucu
    res = model.predict(img_np, imgsz=imgsz, conf=conf, verbose=False)[0]
    fire_prob = 0.0
    smoke_prob = 0.0
    boxes = []
    for b in res.boxes:
        cls = int(b.cls)
        p = float(b.conf)
        x1,y1,x2,y2 = map(float, b.xyxy[0])
        boxes.append([x1,y1,x2,y2,p,cls])
        # 0=fire, 1=smoke varsayımı; kendi modelin böyle eğitilecek
        if cls == 0: fire_prob = max(fire_prob, p)
        if cls == 1: smoke_prob = max(smoke_prob, p)
    decision = "fire_detected" if fire_prob >= 0.7 else "no_fire"
    return fire_prob, smoke_prob, boxes, decision

@app.post("/classify-image")
def classify_image(req: ImageReq):
    img_bytes = base64.b64decode(req.image_base64)
    img = np.array(Image.open(io.BytesIO(img_bytes)).convert("RGB"))
    f, s, boxes, dec = infer_np(img)
    return {"fire_prob": f, "smoke_prob": s, "bbox": boxes, "decision": dec}

streams = {}

@app.post("/classify-stream/start")
def start_stream(cfg: StreamCfg):
    key = cfg.stream_url
    if key in streams and streams[key]["running"]:
        return {"status":"already_running"}

    streams[key] = {"running": True}

    def worker():
        # USB kamera için "0" / "1" vs. sayı verilebilir
        cap = cv2.VideoCapture(0 if cfg.stream_url.isdigit() else cfg.stream_url)
        q = []  # (t, prob)
        last_push = 0.0
        while streams.get(key, {}).get("running", False):
            ok, frame = cap.read()
            if not ok:
                time.sleep(0.2); continue
            # FPS’i korumak istersen her 2–3 framede bir bak: (örn. mod sayacı)
            f, _, _, _ = infer_np(frame, conf=0.25, imgsz=512)
            now = time.time()
            q.append((now, f))
            q[:] = [(t,p) for (t,p) in q if now - t <= cfg.window_seconds]
            strong = [p for (_,p) in q if p >= cfg.prob_threshold]
            if len(strong) >= cfg.min_frames and now - last_push > 10:
                try:
                    requests.post(cfg.webhook_url, json={
                        "type":"sustained_fire_signal",
                        "stream_url": cfg.stream_url,
                        "fire_prob": float(max(strong)),
                        "at": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime())
                    }, timeout=3)
                    last_push = now
                except Exception:
                    pass
        cap.release()

    th = threading.Thread(target=worker, daemon=True)
    th.start()
    return {"status":"started"}

@app.post("/classify-stream/stop")
def stop_stream(cfg: StreamCfg):
    key = cfg.stream_url
    if key in streams:
        streams[key]["running"] = False
    return {"status":"stopped"}
