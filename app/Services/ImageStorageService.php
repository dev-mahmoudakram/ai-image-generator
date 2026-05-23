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
