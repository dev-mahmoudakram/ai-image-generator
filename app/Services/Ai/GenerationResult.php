<?php

namespace App\Services\Ai;

use Laravel\Ai\Responses\ImageResponse;

readonly class GenerationResult
{
    public function __construct(
        public string        $provider,
        public string        $model,
        public string        $prompt,
        public ?ImageResponse $response   = null,  // Gemini (Laravel AI SDK)
        public ?string       $imageBytes = null,   // HuggingFace (raw binary)
    ) {}
}
