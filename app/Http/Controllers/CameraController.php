<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CameraController extends Controller
{
    public function start(Request $r)
    {
        $aiBase = config('services.ai.base_url', env('AI_BASE_URL'));
        $payload = [
            'stream_url'     => $r->input('stream_url'),
            'webhook_url'    => route('ai.hook'),
            'min_frames'     => (int) env('AI_MIN_FRAMES', 10),
            'prob_threshold' => (float) env('AI_PROB_THRESHOLD', 0.7),
            'window_seconds' => (int) env('AI_WINDOW_SECONDS', 5),
        ];
        $res = Http::post($aiBase . '/classify-stream/start', $payload)->json();
        return response()->json($res);
    }

    public function stop(Request $r)
    {
        $aiBase = config('services.ai.base_url', env('AI_BASE_URL'));
        $payload = [
            'stream_url'     => $r->input('stream_url'),
            'webhook_url'    => route('ai.hook'),
            'min_frames'     => (int) env('AI_MIN_FRAMES', 10),
            'prob_threshold' => (float) env('AI_PROB_THRESHOLD', 0.7),
            'window_seconds' => (int) env('AI_WINDOW_SECONDS', 5),
        ];
        $res = Http::post($aiBase . '/classify-stream/stop', $payload)->json();
        return response()->json($res);
    }
}
