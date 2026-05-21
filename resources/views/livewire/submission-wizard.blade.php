<div style="max-width:560px;margin:0 auto;">

    {{-- Step indicator --}}
    <div class="steps" style="margin-bottom:var(--space-6);">
        <div class="steps__item {{ $step === 'form' ? 'steps__item--active' : 'steps__item--done' }}">
            1. Your info
        </div>
        <div class="steps__item {{ $step === 'selfie' ? 'steps__item--active' : ($step === 'template' ? 'steps__item--done' : '') }}">
            2. Selfie
        </div>
        <div class="steps__item {{ $step === 'template' ? 'steps__item--active' : '' }}">
            3. Choose style
        </div>
    </div>

    {{-- ── Step 1: Contact form ─────────────────────────────────────────────── --}}
    @if($step === 'form')
        <div class="card">
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 var(--space-1);">Create your portrait</h1>
            <p style="color:var(--color-text-dim);font-size:14px;margin:0 0 var(--space-5);">
                Fill in your details to get started.
            </p>

            <form wire:submit="submitForm" novalidate>
                <div class="field">
                    <label class="field__label" for="name">Full name</label>
                    <input
                        class="field__input"
                        type="text"
                        id="name"
                        wire:model.blur="name"
                        autocomplete="name"
                        placeholder="Your name"
                    >
                    @error('name') <p class="field__error">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label class="field__label" for="phone">Phone number</label>
                    <input
                        class="field__input"
                        type="tel"
                        id="phone"
                        wire:model.blur="phone"
                        autocomplete="tel"
                        placeholder="+966 5X XXX XXXX"
                        dir="ltr"
                    >
                    @error('phone') <p class="field__error">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label class="field__label" for="email">Email <span style="color:var(--color-text-dim);font-weight:400;">(optional)</span></label>
                    <input
                        class="field__input"
                        type="email"
                        id="email"
                        wire:model.blur="email"
                        autocomplete="email"
                        placeholder="you@example.com"
                    >
                    @error('email') <p class="field__error">{{ $message }}</p> @enderror
                </div>

                <label class="checkbox" style="margin-bottom:var(--space-5);">
                    <input type="checkbox" wire:model="consent">
                    <span>I consent to my selfie being processed by AI to generate a portrait image. I can request deletion at any time.</span>
                </label>
                @error('consent') <p class="field__error" style="margin-top:calc(-1 * var(--space-3));margin-bottom:var(--space-4);">{{ $message }}</p> @enderror

                <button type="submit" class="btn btn--primary btn--block" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitForm">Continue &rarr;</span>
                    <span wire:loading wire:target="submitForm">Please wait...</span>
                </button>
            </form>
        </div>

    {{-- ── Step 2: Selfie capture ────────────────────────────────────────────── --}}
    @elseif($step === 'selfie')
        <div class="card">
            <h2 style="font-size:1.25rem;font-weight:700;margin:0 0 var(--space-1);">Take a selfie</h2>
            <p style="color:var(--color-text-dim);font-size:14px;margin:0 0 var(--space-5);">
                Face the camera straight-on in good lighting for best results.
            </p>

            {{-- Camera --}}
            <div x-data="cameraCapture">
                <div class="camera">
                    <video
                        x-ref="video"
                        class="camera__preview"
                        x-show="streaming"
                        autoplay
                        muted
                        playsinline
                        style="display:none;"
                    ></video>

                    <canvas
                        x-ref="canvas"
                        class="camera__preview"
                        x-show="captured"
                        style="display:none;"
                    ></canvas>

                    {{-- Idle placeholder --}}
                    <div
                        class="camera__preview"
                        x-show="!streaming && !captured"
                        style="display:flex;align-items:center;justify-content:center;color:var(--color-text-dim);flex-direction:column;gap:var(--space-3);"
                    >
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        <span style="font-size:14px;">Camera preview will appear here</span>
                    </div>

                    <div class="camera__actions">
                        <button
                            type="button"
                            class="btn btn--primary"
                            x-show="!streaming && !captured"
                            @click="start()"
                            style="display:none;"
                        >Open camera</button>

                        <button
                            type="button"
                            class="btn btn--primary"
                            x-show="streaming"
                            @click="capture()"
                            style="display:none;"
                        >Take photo</button>

                        <button
                            type="button"
                            class="btn"
                            x-show="streaming"
                            @click="stop()"
                            style="display:none;"
                        >Cancel</button>

                        <button
                            type="button"
                            class="btn"
                            x-show="captured"
                            @click="retake()"
                            style="display:none;"
                        >Retake</button>
                    </div>

                    <p class="camera__error" x-show="error" x-text="error" style="display:none;"></p>
                </div>

                <div wire:loading wire:target="selfie" style="text-align:center;padding:var(--space-4);color:var(--color-text-dim);font-size:14px;">
                    Uploading selfie&hellip;
                </div>
            </div>

            {{-- File upload fallback --}}
            <div style="margin-top:var(--space-5);">
                <p style="text-align:center;font-size:13px;color:var(--color-text-dim);margin-bottom:var(--space-3);">
                    &mdash; or upload from your device &mdash;
                </p>
                <div class="field" style="margin:0;">
                    <label class="field__label" for="selfie-file">Choose a photo</label>
                    <input
                        class="field__input"
                        type="file"
                        id="selfie-file"
                        wire:model="selfie"
                        accept="image/jpeg,image/png,image/webp"
                    >
                    @error('selfie') <p class="field__error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

    {{-- ── Step 3: Template selection ─────────────────────────────────────────── --}}
    @elseif($step === 'template')
        <div style="margin-bottom:var(--space-5);">
            <h2 style="font-size:1.25rem;font-weight:700;margin:0 0 var(--space-1);">Choose your style</h2>
            <p style="color:var(--color-text-dim);font-size:14px;margin:0;">
                Pick the look you want for your AI portrait.
            </p>
        </div>

        <div wire:loading wire:target="chooseTemplate" style="text-align:center;padding:var(--space-8);color:var(--color-text-dim);">
            <p>Queuing your generation&hellip;</p>
        </div>

        <div wire:loading.remove wire:target="chooseTemplate">
            @if($templates->isEmpty())
                <div class="card" style="text-align:center;padding:var(--space-8);">
                    <p style="color:var(--color-text-dim);">No styles available right now. Please try again later.</p>
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--space-4);">
                    @foreach($templates as $template)
                        <button
                            type="button"
                            class="card template-card"
                            wire:click="chooseTemplate({{ $template->id }})"
                            wire:loading.attr="disabled"
                            style="padding:var(--space-3);"
                        >
                            <img
                                src="{{ $template->thumbnailUrl() ?? $template->imageUrl() }}"
                                alt="{{ $template->title }}"
                                style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:var(--radius);margin-bottom:var(--space-3);"
                            >
                            <div style="font-weight:500;font-size:14px;">{{ $template->title }}</div>
                            @if($template->description)
                                <div style="font-size:12px;color:var(--color-text-dim);margin-top:var(--space-1);">
                                    {{ Str::limit($template->description, 80) }}
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
