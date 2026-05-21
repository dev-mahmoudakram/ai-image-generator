@extends('layouts.admin')
@section('title', 'New template')

@section('content')
    <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);">
        <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">&larr; Back</a>
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">New template</h1>
    </div>

    <div class="card" style="max-width:600px;">
        <form method="POST" action="{{ route('admin.templates.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label class="field__label" for="title">Title *</label>
                <input class="field__input" type="text" id="title" name="title"
                       value="{{ old('title') }}" required>
                @error('title') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="description">Description</label>
                <textarea class="field__textarea" id="description" name="description"
                          rows="3">{{ old('description') }}</textarea>
                @error('description') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label" for="prompt_hint">Prompt hint</label>
                <textarea class="field__textarea" id="prompt_hint" name="prompt_hint"
                          rows="2" placeholder="Additional guidance appended to the base AI prompt...">{{ old('prompt_hint') }}</textarea>
                @error('prompt_hint') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div class="field">
                    <label class="field__label" for="sort_order">Sort order</label>
                    <input class="field__input" type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', 0) }}" min="0">
                </div>
                <div class="field" style="display:flex;align-items:center;gap:var(--space-2);padding-top:var(--space-6);">
                    <label class="checkbox">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>
            </div>

            <div class="field">
                <label class="field__label" for="image">Template image *</label>
                <input class="field__input" type="file" id="image" name="image"
                       accept="image/jpeg,image/png,image/webp" required>
                <p style="font-size:12px;color:var(--color-text-dim);margin-top:var(--space-1);">
                    JPG, PNG, or WebP &mdash; max 8 MB
                </p>
                @error('image') <p class="field__error">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex;gap:var(--space-3);">
                <button type="submit" class="btn btn--primary">Create template</button>
                <a href="{{ route('admin.templates.index') }}" class="btn btn--ghost">Cancel</a>
            </div>
        </form>
    </div>
@endsection
