<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

        return Storage::disk($this->disk)->url($this->path);
    }
}
