<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int    $id
 * @property int    $submission_id
 * @property string $kind
 * @property string $disk
 * @property string $path
 * @property string $mime_type
 * @property int    $size_bytes
 * @property int    $width
 * @property int    $height
 */
class SubmissionAsset extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'kind',
        'disk',
        'path',
        'mime_type',
        'size_bytes',
        'width',
        'height',
    ];

    protected $casts = [
        'size_bytes'  => 'integer',
        'width'       => 'integer',
        'height'      => 'integer',
        'created_at'  => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function url(): string
    {
        if ($this->disk === 'public') {
            return '/storage/'.ltrim($this->path, '/');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);

        return $disk->url($this->path);
    }
}
