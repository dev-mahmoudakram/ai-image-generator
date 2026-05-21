<?php

namespace App\Services\Ai;

use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Files\Image as AiImage;
use Laravel\Ai\Image;

class AiImageGenerationService
{
    public function generate(GenerationRequest $request): GenerationResult
    {
        $provider = config('ai.default_for_images', 'gemini');
        $model    = config('ai.image_model', 'gemini-2.0-flash-preview-image-generation');
        $timeout  = (int) config('ai.image_timeout', 120);
        $quality  = config('ai.image_quality', 'high');

        $response = Image::of($request->prompt)
            ->attachments([
                AiImage::fromStorage($request->selfiePath,   $request->selfieDisk),
                AiImage::fromStorage($request->templatePath, $request->templateDisk),
            ])
            ->quality($quality)
            ->timeout($timeout)
            ->generate(Lab::from($provider), $model);

        return new GenerationResult(
            response: $response,
            provider: $provider,
            model:    $model,
            prompt:   $request->prompt,
        );
    }

    public function buildPrompt(string $promptHint = ''): string
    {
        $base = 'Generate a photorealistic portrait of the person from the selfie image, '
            . 'styled to match the visual aesthetic and world of the reference template image. '
            . 'Preserve the person\'s facial identity, skin tone, and unique features exactly. '
            . 'Apply the template\'s visual style, lighting, environment, color palette, and mood.';

        if ($promptHint = trim($promptHint)) {
            $base .= ' ' . $promptHint;
        }

        return $base;
    }
}
