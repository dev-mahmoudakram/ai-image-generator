@extends('layouts.admin')
@section('title', 'Edit — ' . $template->title)

@section('content')
    <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);">
        <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">&larr; Back</a>
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Edit template</h1>
    </div>

    <div class="card" style="max-width:600px;">
        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="field">
                <label class="field__label" for="title">Title *</label>
                <input class="field__input" type="text" id="title" name="title"
                       value="{{ old('title', $template->title) }}" required>
                @error('title') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="description">Description</label>
                <textarea class="field__textarea" id="description" name="description"
                          rows="3">{{ old('description', $template->description) }}</textarea>
                @error('description') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="prompt_hint">Prompt hint</label>
                <textarea class="field__textarea" id="prompt_hint" name="prompt_hint"
                          rows="2">{{ old('prompt_hint', $template->prompt_hint) }}</textarea>
                @error('prompt_hint') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div class="field">
                    <label class="field__label" for="sort_order">Sort order</label>
                    <input class="field__input" type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', $template->sort_order) }}" min="0">
                </div>
                <div class="field" style="display:flex;align-items:center;gap:var(--space-2);padding-top:var(--space-6);">
                    <label class="checkbox">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>
            </div>

            <div class="field">
                <label class="field__label" for="image">Replace image</label>
                @if($template->image_path !== 'templates/placeholder.jpg')
                    <div style="margin-bottom:var(--space-2);">
                        <img src="{{ $template->thumbnailUrl() ?? $template->imageUrl() }}"
                             alt="{{ $template->title }}"
                             style="height:80px;width:80px;object-fit:cover;border-radius:var(--radius);">
                    </div>
                @endif
                <input class="field__input" type="file" id="image" name="image"
                       accept="image/jpeg,image/png,image/webp">
                <p style="font-size:12px;color:var(--color-text-dim);margin-top:var(--space-1);">
                    Leave blank to keep current image. JPG, PNG, or WebP &mdash; max 8 MB.
                </p>
                @error('image') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex;gap:var(--space-3);">
                <button type="submit" class="btn btn--primary">Save changes</button>
                <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>
@endsection
