<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\SubmissionAsset;
use App\Models\Template;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Responses\ImageResponse;

class ImageStorageService
{
    private const DISK = 'public';

    // ── Selfie ────────────────────────────────────────────────────────────────

    public function storeSelfie(Submission $submission, UploadedFile $file): SubmissionAsset
    {
        $dir  = "submissions/{$submission->uuid}";
        $ext  = $file->getClientOriginalExtension() ?: 'jpg';
        $name = "selfie.{$ext}";

        $path = $file->storeAs($dir, $name, ['disk' => self::DISK]);

        [$width, $height] = $this->imageDimensions(
            Storage::disk(self::DISK)->path($path)
        );

        return $submission->assets()->create([
            'kind'       => 'selfie',
            'disk'       => self::DISK,
            'path'       => $path,
            'mime_type'  => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'width'      => $width,
            'height'     => $height,
        ]);
    }

    // ── Generated image (Gemini / Laravel AI SDK) ─────────────────────────────

    public function storeGeneratedImage(Submission $submission, ImageResponse $response): SubmissionAsset
    {
        $dir  = "submissions/{$submission->uuid}";
        $name = 'generated_' . now()->timestamp . '.png';

        $path = $response->storeAs($dir, $name, disk: self::DISK);

        $fullPath = Storage::disk(self::DISK)->path($path);
        [$width, $height] = $this->imageDimensions($fullPath);

        return $submission->assets()->create([
            'kind'       => 'generated',
            'disk'       => self::DISK,
            'path'       => $path,
            'mime_type'  => 'image/png',
            'size_bytes' => filesize($fullPath),
            'width'      => $width,
            'height'     => $height,
        ]);
    }

    // ── Generated image (HuggingFace / raw bytes) ─────────────────────────────

    public function storeRawImage(Submission $submission, string $bytes): SubmissionAsset
    {
        $dir  = "submissions/{$submission->uuid}";
        $name = 'generated_' . now()->timestamp . '.png';
        $path = "{$dir}/{$name}";

        Storage::disk(self::DISK)->put($path, $bytes);

        $fullPath = Storage::disk(self::DISK)->path($path);
        [$width, $height] = $this->imageDimensions($fullPath);

        return $submission->assets()->create([
            'kind'       => 'generated',
            'disk'       => self::DISK,
            'path'       => $path,
            'mime_type'  => 'image/png',
            'size_bytes' => strlen($bytes),
            'width'      => $width,
            'height'     => $height,
        ]);
    }

    // ── Template images ───────────────────────────────────────────────────────

    public function storeTemplateImage(Template $template, UploadedFile $file): array
    {
        $dir  = "templates/{$template->id}";
        $ext  = $file->getClientOriginalExtension() ?: 'jpg';

        $imagePath = $file->storeAs($dir, "image.{$ext}", ['disk' => self::DISK]);

        return [
            'disk'           => self::DISK,
            'image_path'     => $imagePath,
            'thumbnail_path' => null,
        ];
    }

    // ── Frame compositing ─────────────────────────────────────────────────────

    public function applyFrame(SubmissionAsset $asset): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $framePath = resource_path('assets/frame.png');
        $fullPath  = Storage::disk($asset->disk)->path($asset->path);

        if (! file_exists($framePath) || ! file_exists($fullPath)) {
            return;
        }

        $frame  = imagecreatefrompng($framePath);
        $frameW = imagesx($frame);
        $frameH = imagesy($frame);

        $source = imagecreatefromstring(file_get_contents($fullPath));
        $srcW   = imagesx($source);
        $srcH   = imagesy($source);

        // Cover-fit: determine which portion of the source to use so the result
        // fills the frame exactly without distortion (center-crop).
        $frameRatio = $frameW / $frameH;
        $srcRatio   = $srcW   / $srcH;

        if ($srcRatio > $frameRatio) {
            // Source is wider — fit by height, crop sides
            $cropH   = $srcH;
            $cropW   = (int) round($srcH * $frameRatio);
            $cropX   = (int) round(($srcW - $cropW) / 2);
            $cropY   = 0;
        } else {
            // Source is taller — fit by width, crop top/bottom
            $cropW   = $srcW;
            $cropH   = (int) round($srcW / $frameRatio);
            $cropX   = 0;
            $cropY   = (int) round(($srcH - $cropH) / 2);
        }

        $canvas = imagecreatetruecolor($frameW, $frameH);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // Scale cropped source region into the canvas
        imagecopyresampled($canvas, $source, 0, 0, $cropX, $cropY, $frameW, $frameH, $cropW, $cropH);

        // Composite frame on top (frame PNG has transparent window for the photo)
        imagealphablending($canvas, true);
        imagecopy($canvas, $frame, 0, 0, 0, 0, $frameW, $frameH);

        imagepng($canvas, $fullPath);

        imagedestroy($frame);
        imagedestroy($source);
        imagedestroy($canvas);

        clearstatcache(true, $fullPath);

        $asset->update([
            'width'      => $frameW,
            'height'     => $frameH,
            'mime_type'  => 'image/png',
            'size_bytes' => filesize($fullPath),
        ]);
    }

    // ── Deletion ──────────────────────────────────────────────────────────────

    public function deleteAsset(SubmissionAsset $asset): void
    {
        Storage::disk($asset->disk)->delete($asset->path);
    }

    public function deleteTemplateImages(Template $template): void
    {
        $disk = Storage::disk($template->disk);

        if ($template->image_path) {
            $disk->delete($template->image_path);
        }
        if ($template->thumbnail_path) {
            $disk->delete($template->thumbnail_path);
        }
    }

    // ── URL ───────────────────────────────────────────────────────────────────

    public function url(SubmissionAsset $asset): string
    {
        if ($asset->disk === 'public') {
            return '/storage/'.ltrim($asset->path, '/');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($asset->disk);

        return $disk->url($asset->path);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function imageDimensions(string $absolutePath): array
    {
        if (! file_exists($absolutePath)) {
            return [null, null];
        }

        $size = @getimagesize($absolutePath);

        return $size ? [$size[0], $size[1]] : [null, null];
    }
}
