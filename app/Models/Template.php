<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'disk',
        'image_path',
        'thumbnail_path',
        'prompt_hint',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'selected_template_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        return $this->publicUrl($this->image_path);
    }

    public function thumbnailUrl(): ?string
    {
        $path = $this->thumbnail_path ?? $this->image_path;

        return $this->publicUrl($path);
    }

    private function publicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if ($this->disk === 'public') {
            return '/storage/'.ltrim($path, '/');
        }

        return Storage::disk($this->disk)->url($path);
    }
}
