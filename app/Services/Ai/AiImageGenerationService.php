<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Files\Image as AiImage;
use Laravel\Ai\Image;
use RuntimeException;

class AiImageGenerationService
{
    public function generate(GenerationRequest $request): GenerationResult
    {
        $provider = config('ai.default_for_images', 'gemini');

        return match ($provider) {
            'huggingface' => $this->generateWithHuggingFace($request),
            'pollinations' => $this->generateWithPollinations($request),
            default        => $this->generateWithGemini($request),
        };
    }

    // ── Gemini (Laravel AI SDK) ───────────────────────────────────────────────

    private function generateWithGemini(GenerationRequest $request): GenerationResult
    {
        $model   = config('ai.image_model', 'gemini-2.0-flash-preview-image-generation');
        $timeout = (int) config('ai.image_timeout', 120);
        $quality = config('ai.image_quality', 'high');

        $response = Image::of($request->prompt)
            ->attachments([
                AiImage::fromStorage($request->selfiePath,   $request->selfieDisk),
                AiImage::fromStorage($request->templatePath, $request->templateDisk),
            ])
            ->quality($quality)
            ->timeout($timeout)
            ->generate(Lab::from('gemini'), $model);

        if ($response->count() === 0) {
            throw new RuntimeException(
                "Gemini returned no images using model [{$model}]. " .
                "Verify the model name supports image output and that the request was not filtered."
            );
        }

        return new GenerationResult(
            provider:   'gemini',
            model:      $model,
            prompt:     $request->prompt,
            response:   $response,
        );
    }

    // ── HuggingFace Inference API ─────────────────────────────────────────────

    private function generateWithHuggingFace(GenerationRequest $request): GenerationResult
    {
        $token   = config('ai.huggingface.token');
        $model   = config('ai.huggingface.model', 'timbrooks/instruct-pix2pix');
        $timeout = (int) config('ai.huggingface.timeout', 120);

        if (empty($token)) {
            throw new RuntimeException('HuggingFace token is not configured (HF_TOKEN).');
        }

        $imageBytes = Storage::disk($request->selfieDisk)->get($request->selfiePath);
        $base64     = base64_encode($imageBytes);

        $response = Http::withToken($token)
            ->timeout($timeout)
            ->post("https://router.huggingface.co/hf-inference/models/{$model}", [
                'inputs'     => $base64,
                'parameters' => [
                    'prompt'               => $request->prompt,
                    'num_inference_steps'  => 25,
                    'image_guidance_scale' => 1.5,
                    'guidance_scale'       => 7.5,
                ],
            ]);

        if ($response->failed()) {
            $body = $response->json();
            $error = $body['error'] ?? $response->body();
            throw new RuntimeException("HuggingFace API error: {$error}");
        }

        return new GenerationResult(
            provider:   'huggingface',
            model:      $model,
            prompt:     $request->prompt,
            imageBytes: $response->body(),
        );
    }

    // ── Pollinations.ai (free, no key) ───────────────────────────────────────

    private function generateWithPollinations(GenerationRequest $request): GenerationResult
    {
        $model  = config('ai.pollinations.model', 'flux-realism');
        $width  = (int) config('ai.pollinations.width', 1024);
        $height = (int) config('ai.pollinations.height', 1024);
        $seed   = random_int(1, 999999);

        $url = 'https://image.pollinations.ai/prompt/' . rawurlencode($request->prompt)
             . "?width={$width}&height={$height}&model={$model}&seed={$seed}&nologo=true";

        $response = Http::timeout(90)->get($url);

        if ($response->failed()) {
            throw new RuntimeException("Pollinations error {$response->status()}: {$response->body()}");
        }

        return new GenerationResult(
            provider:   'pollinations',
            model:      $model,
            prompt:     $request->prompt,
            imageBytes: $response->body(),
        );
    }

    // ── Prompt builder ────────────────────────────────────────────────────────

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
