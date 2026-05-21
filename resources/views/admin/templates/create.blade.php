<x-layouts.admin title="New template">
    <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);">
        <a href="{{ route('admin.templates.index') }}" class="btn btn--sm btn--ghost">&larr; Back</a>
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">New template</h1>
    </div>

    <div class="card" style="max-width:600px;">
        <form method="POST" action="{{ route('admin.templates.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="title">Title <span style="color:var(--color-error,#991b1b);">*</span></label>
                <input class="form-input @error('title') form-input--error @enderror"
                       type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-input @error('description') form-input--error @enderror"
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="prompt_hint">Prompt hint</label>
                <textarea class="form-input @error('prompt_hint') form-input--error @enderror"
                          id="prompt_hint" name="prompt_hint" rows="2"
                          placeholder="Additional guidance appended to the base AI prompt...">{{ old('prompt_hint') }}</textarea>
                @error('prompt_hint')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div class="form-group">
                    <label class="form-label" for="sort_order">Sort order</label>
                    <input class="form-input" type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', 0) }}" min="0">
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:var(--space-2);padding-top:var(--space-6);">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }} style="width:1rem;height:1rem;">
                    <label for="is_active" class="form-label" style="margin:0;">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="image">Template image <span style="color:var(--color-error,#991b1b);">*</span></label>
                <input class="form-input @error('image') form-input--error @enderror"
                       type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" required>
                <p style="font-size:0.75rem;color:var(--color-text-muted);margin-top:var(--space-1);">
                    JPG, PNG, or WebP &mdash; max 8 MB
                </p>
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;gap:var(--space-3);margin-top:var(--space-2);">
                <button type="submit" class="btn btn--primary">Create template</button>
                <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
