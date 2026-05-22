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
        $base = <<<'PROMPT'
Use Image A as the identity reference and Image B as the scene, pose, outfit, and composition reference.

Replace the man in Image B with the person from Image A. Preserve the facial identity, skin tone, and recognizable features of the person in Image A, while matching the overall look, traditional Saudi clothing, pose, and atmosphere of Image B.

The final image should show the person from Image A standing in the same style and setting as Image B, wearing authentic traditional Saudi attire, with a realistic and elegant appearance. Keep the Saudi-inspired heritage background and premium cultural atmosphere.

Important:
- Preserve the identity of the person in Image A
- Use Image B only as style, clothing, pose, and composition reference
- Do not copy watermarks
- Do not recreate the image as an exact clone
- Keep the final result photorealistic, clean, high-quality, and visually premium
- Maintain natural facial proportions and realistic lighting
PROMPT;

        if ($promptHint = trim($promptHint)) {
            $base .= "\n\nTemplate style note:\n{$promptHint}";
        }

        return $base;
    }
}
