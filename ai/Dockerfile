FROM python:3.11-slim

WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends \
    ffmpeg libglib2.0-0 libsm6 libxext6 libxrender-dev \
 && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Model klasörü
RUN mkdir -p /app/models
COPY app.py ./
# Eğer elinde eğitilmiş model varsa projeye şu yolu izleyerek kopyala:
# COPY models/fire_smoke_best.pt /app/models/fire_smoke_best.pt

EXPOSE 9000
CMD ["uvicorn", "app:app", "--host", "0.0.0.0", "--port", "9000"]
