@extends('layouts.admin')
@section('title', 'Edit — ' . $template->title)

@section('content')
    <div class="admin-page-header" style="justify-content:flex-start;">
        <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">&larr; Back</a>
        <div>
            <span class="eyebrow">Creative library</span>
            <h1 class="admin-page-header__title">Edit template</h1>
        </div>
    </div>

    <div class="card premium-card" style="max-width:720px;">
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

            <div class="form-grid form-grid--two">
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
                <label class="field__label">Replace image</label>
                <x-file-drop
                    name="image"
                    hint="Drop a new image to replace — JPG, PNG, or WebP, max 8 MB"
                />
                @error('image') <p class="field__error" style="margin-top:var(--space-2);">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex;gap:var(--space-3);flex-wrap:wrap;">
                <button type="submit" class="btn btn--primary">Save changes</button>
                <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>
@endsection
