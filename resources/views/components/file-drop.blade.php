@props([
    'name'     => 'file',
    'uploadTo' => null,
    'accept'   => 'image/jpeg,image/png,image/webp',
    'hint'     => 'JPG, PNG, or WebP — max 8 MB',
])

<div x-data="fileDrop()">
    <div
        class="drop-zone"
        :class="{ 'drop-zone--active': dragging }"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="onDrop($event)"
        @click="$refs.input.click()"
        role="button"
        tabindex="0"
        @keydown.enter.prevent="$refs.input.click()"
    >
        {{-- Image preview (absolutely positioned, covers body when shown) --}}
        <img
            x-show="previewUrl"
            :src="previewUrl"
            alt="Preview"
            class="drop-zone__img"
            style="display:none;"
        >

        {{-- Placeholder — always rendered; preview image covers it when selected --}}
        <div class="drop-zone__body">
            <svg class="drop-zone__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
            <p class="drop-zone__title">
                Drop your file here<br>
                <span>or click to browse</span>
            </p>
            <p class="drop-zone__hint">{{ $hint }}</p>
        </div>

        {{-- File name chip --}}
        <div class="drop-zone__badge" x-show="fileName" x-text="fileName" style="display:none;"></div>

        {{-- Upload spinner overlay --}}
        <div class="drop-zone__overlay" x-show="uploading" style="display:none;">
            <div class="spinner"></div>
        </div>

        {{-- Hidden file input — wire:model added when inside a Livewire component --}}
        <input
            x-ref="input"
            type="file"
            name="{{ $name }}"
            @if($uploadTo) wire:model="{{ $uploadTo }}" @endif
            accept="{{ $accept }}"
            @change="onInputChange()"
            style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;"
        >
    </div>

    {{-- Upload error (outside .drop-zone so overflow:hidden doesn't clip it) --}}
    <p x-show="errorMsg" x-text="errorMsg"
       style="display:none;color:var(--color-danger);font-size:13px;margin-top:var(--space-2);"></p>
</div>
