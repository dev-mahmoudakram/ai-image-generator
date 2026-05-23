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

        // Detect the opaque-white photo window inside the frame by scanning the
        // centre row and centre column for near-white pixels (r,g,b > 200, alpha = 0).
        [$winX, $winY, $winW, $winH] = $this->detectPhotoWindow($frame, $frameW, $frameH);

        $source = imagecreatefromstring(file_get_contents($fullPath));
        $srcW   = imagesx($source);
        $srcH   = imagesy($source);

        // Cover-fit the photo into the window (center-crop without distortion).
        $winRatio = $winW / $winH;
        $srcRatio = $srcW  / $srcH;

        if ($srcRatio > $winRatio) {
            // Source wider than window — fit by height, crop sides
            $cropH = $srcH;
            $cropW = (int) round($srcH * $winRatio);
            $cropX = (int) round(($srcW - $cropW) / 2);
            $cropY = 0;
        } else {
            // Source taller than window — fit by width, crop top/bottom
            $cropW = $srcW;
            $cropH = (int) round($srcW / $winRatio);
            $cropX = 0;
            $cropY = (int) round(($srcH - $cropH) / 2);
        }

        // Draw frame first (background), then photo into the white window on top.
        $canvas = imagecreatetruecolor($frameW, $frameH);
        imagecopy($canvas, $frame, 0, 0, 0, 0, $frameW, $frameH);
        imagecopyresampled($canvas, $source, $winX, $winY, $cropX, $cropY, $winW, $winH, $cropW, $cropH);

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

    private function detectPhotoWindow(\GdImage $frame, int $w, int $h): array
    {
        $threshold = 200;
        $cx = (int) ($w / 2);
        $cy = (int) ($h / 2);

        $top = 0; $bottom = $h - 1;
        for ($y = 0; $y < $h; $y++) {
            $c = imagecolorsforindex($frame, imagecolorat($frame, $cx, $y));
            if ($c['red'] > $threshold && $c['green'] > $threshold && $c['blue'] > $threshold && $c['alpha'] === 0) {
                $top = $y; break;
            }
        }
        for ($y = $h - 1; $y >= 0; $y--) {
            $c = imagecolorsforindex($frame, imagecolorat($frame, $cx, $y));
            if ($c['red'] > $threshold && $c['green'] > $threshold && $c['blue'] > $threshold && $c['alpha'] === 0) {
                $bottom = $y; break;
            }
        }

        $left = 0; $right = $w - 1;
        for ($x = 0; $x < $w; $x++) {
            $c = imagecolorsforindex($frame, imagecolorat($frame, $x, $cy));
            if ($c['red'] > $threshold && $c['green'] > $threshold && $c['blue'] > $threshold && $c['alpha'] === 0) {
                $left = $x; break;
            }
        }
        for ($x = $w - 1; $x >= 0; $x--) {
            $c = imagecolorsforindex($frame, imagecolorat($frame, $x, $cy));
            if ($c['red'] > $threshold && $c['green'] > $threshold && $c['blue'] > $threshold && $c['alpha'] === 0) {
                $right = $x; break;
            }
        }

        return [$left, $top, $right - $left + 1, $bottom - $top + 1];
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
