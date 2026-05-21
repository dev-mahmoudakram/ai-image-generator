<x-layouts.admin title="Edit — {{ $template->title }}">
    <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);">
        <a href="{{ route('admin.templates.index') }}" class="btn btn--sm btn--ghost">&larr; Back</a>
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Edit template</h1>
    </div>

    <div class="card" style="max-width:600px;">
        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="title">Title <span style="color:var(--color-error,#991b1b);">*</span></label>
                <input class="form-input @error('title') form-input--error @enderror"
                       type="text" id="title" name="title" value="{{ old('title', $template->title) }}" required>
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-input @error('description') form-input--error @enderror"
                          id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                @error('description')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="prompt_hint">Prompt hint</label>
                <textarea class="form-input @error('prompt_hint') form-input--error @enderror"
                          id="prompt_hint" name="prompt_hint" rows="2">{{ old('prompt_hint', $template->prompt_hint) }}</textarea>
                @error('prompt_hint')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div class="form-group">
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input class="form-input" type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', $template->sort_order) }}" min="0">
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:var(--space-2);padding-top:var(--space-6);">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $template->is_active) ? 'checked' : '' }} style="width:1rem;height:1rem;">
                    <label for="is_active" class="form-label" style="margin:0;">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="image">Replace image</label>
                @if($template->image_path !== 'templates/placeholder.jpg')
                    <div style="margin-bottom:var(--space-2);">
                        <img src="{{ $template->thumbnailUrl() ?? $template->imageUrl() }}"
                             alt="{{ $template->title }}"
                             style="height:80px;width:80px;object-fit:cover;border-radius:var(--radius-sm);">
                    </div>
                @endif
                <input class="form-input @error('image') form-input--error @enderror"
                       type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                <p style="font-size:0.75rem;color:var(--color-text-muted);margin-top:var(--space-1);">
                    Leave blank to keep current image. JPG, PNG, or WebP &mdash; max 8 MB.
                </p>
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;gap:var(--space-3);margin-top:var(--space-2);">
                <button type="submit" class="btn btn--primary">Save changes</button>
                <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
