<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class TemplateService
{
    public function __construct(private ImageStorageService $storage) {}

    public function activeOrdered(): Collection
    {
        return Template::active()->ordered()->get();
    }

    public function create(array $data, UploadedFile $image): Template
    {
        $template = Template::create([
            'title'       => $data['title'],
            'slug'        => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'prompt_hint' => $data['prompt_hint'] ?? null,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $data['is_active'] ?? true,
            'disk'        => 'public',
            'image_path'  => 'templates/placeholder.jpg',
        ]);

        $paths = $this->storage->storeTemplateImage($template, $image);
        $template->update($paths);

        return $template->fresh();
    }

    public function update(Template $template, array $data, ?UploadedFile $image = null): Template
    {
        $updates = [
            'title'       => $data['title']       ?? $template->title,
            'description' => $data['description'] ?? $template->description,
            'prompt_hint' => $data['prompt_hint'] ?? $template->prompt_hint,
            'sort_order'  => $data['sort_order']  ?? $template->sort_order,
            'is_active'   => $data['is_active']   ?? $template->is_active,
        ];

        if (isset($data['title'])) {
            $updates['slug'] = Str::slug($data['title']);
        }

        if ($image) {
            $this->storage->deleteTemplateImages($template);
            $updates = array_merge($updates, $this->storage->storeTemplateImage($template, $image));
        }

        $template->update($updates);

        return $template->fresh();
    }

    public function toggleActive(Template $template): Template
    {
        $template->update(['is_active' => ! $template->is_active]);
        return $template->fresh();
    }

    public function delete(Template $template): void
    {
        $this->storage->deleteTemplateImages($template);
        $template->delete();
    }
}
