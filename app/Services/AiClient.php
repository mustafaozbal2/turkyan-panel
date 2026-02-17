<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiClient
{
    protected string $base;

    public function __construct()
    {
        $this->base = rtrim(env('AI_BASE_URL'), '/');
    }

    public function classifyImageFromPath(string $path): array
    {
        $b64 = base64_encode(file_get_contents($path));
        $payload = ['image_base64' => $b64, 'meta' => ['source' => 'user_upload']];
        $res = Http::post($this->base . '/classify-image', $payload)->json();
        return $res ?: [];
    }
}
