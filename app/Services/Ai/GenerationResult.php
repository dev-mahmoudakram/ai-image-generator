<?php

namespace App\Services\Ai;

use Laravel\Ai\Responses\ImageResponse;

readonly class GenerationResult
{
    public function __construct(
        public ImageResponse $response,
        public string $provider,
        public string $model,
        public string $prompt,
    ) {}
}
