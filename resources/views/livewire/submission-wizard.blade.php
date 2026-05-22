<div class="experience-shell">
    @if($step === 'form')
        <div class="hero-grid">
            <section class="hero-panel" aria-labelledby="hero-title">
                <span class="eyebrow" style="color:var(--color-gold-soft);">Saudi AI Portrait</span>
                <h1 id="hero-title" style="margin-top:var(--space-4);">
                    Create a portrait for a Saudi moment.
                </h1>
                <p style="max-width:560px;font-size:1.03rem;margin-top:var(--space-4);">
                    Upload a selfie, choose a Saudi-inspired visual style, and receive a polished AI portrait designed for events, campaigns, and memorable occasions.
                </p>
                <div class="hero-panel__meta" aria-label="Experience highlights">
                    <span class="hero-chip">Heritage inspired</span>
                    <span class="hero-chip">Mobile ready</span>
                    <span class="hero-chip">Private upload flow</span>
                </div>
            </section>

            <section class="wizard-panel" aria-labelledby="form-title">
                @include('livewire.partials.submission-steps', ['step' => $step])

                <div class="card premium-card">
                    <span class="eyebrow">Step 01</span>
                    <h2 id="form-title">Your information</h2>
                    <p>We use these details to prepare and track your portrait request.</p>

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
                            <label class="field__label" for="email">Email <span style="font-weight:600;">(optional)</span></label>
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
                            <span wire:loading.remove wire:target="submitForm">Continue</span>
                            <span wire:loading wire:target="submitForm">Please wait...</span>
                        </button>
                    </form>
                </div>
            </section>
        </div>

    @elseif($step === 'selfie')
        <section class="wizard-panel" aria-labelledby="selfie-title">
            @include('livewire.partials.submission-steps', ['step' => $step])

            <div class="card premium-card">
                <span class="eyebrow">Step 02</span>
                <h2 id="selfie-title">Capture your selfie</h2>
                <p>Face the camera straight-on in good lighting for the best generated result.</p>

                @if($selfieAttached)
                    <div class="success-card">
                        <div class="success-mark">OK</div>
                        <h3>Photo uploaded successfully</h3>
                        <p>Ready to choose your style.</p>
                        <button
                            type="button"
                            class="btn btn--primary btn--block"
                            wire:click="nextStep"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="nextStep">Choose style</span>
                            <span wire:loading wire:target="nextStep">Please wait&hellip;</span>
                        </button>
                    </div>
                @else
                    <div
                        class="selfie-uploader"
                        data-selfie-uploader
                        data-upload-url="{{ $submissionToken ? route('submission.selfie.store', $submissionToken) : '' }}"
                        data-csrf-token="{{ csrf_token() }}"
                    >
                        <div data-selfie-inputs>
                            <div class="camera">
                                <video
                                    data-camera-video
                                    class="camera__preview"
                                    autoplay
                                    muted
                                    playsinline
                                    hidden
                                ></video>

                                <canvas
                                    data-camera-canvas
                                    class="camera__preview"
                                    hidden
                                ></canvas>

                                <div
                                    data-camera-placeholder
                                    class="camera__preview camera-placeholder"
                                >
                                    <span class="camera-placeholder__icon" aria-hidden="true">AI</span>
                                    <span>Camera preview will appear here</span>
                                </div>

                                <div class="camera__actions">
                                    <button type="button" class="btn btn--primary" data-camera-open>Open camera</button>
                                    <button type="button" class="btn btn--primary" data-camera-capture hidden>Take photo</button>
                                    <button type="button" class="btn" data-camera-cancel hidden>Cancel</button>
                                    <button type="button" class="btn" data-camera-retake hidden>Retake</button>
                                </div>

                                <p data-selfie-status class="camera-note" hidden></p>
                            </div>

                            <div class="upload-divider">
                                <span>or upload from your device</span>
                            </div>

                            <div
                                class="drop-zone"
                                data-selfie-drop-zone
                                role="button"
                                tabindex="0"
                            >
                                <img
                                    data-selfie-preview
                                    alt="Selected selfie preview"
                                    class="drop-zone__img"
                                    hidden
                                >

                                <div class="drop-zone__body">
                                    <svg class="drop-zone__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <p class="drop-zone__title">
                                        Drop your file here<br>
                                        <span>or click to browse</span>
                                    </p>
                                    <p class="drop-zone__hint">JPG, PNG, or WebP. Max 8 MB.</p>
                                </div>

                                <div class="drop-zone__badge" data-selfie-file-name hidden></div>

                                <input
                                    data-selfie-file
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;"
                                >
                            </div>
                        </div>

                        <div data-selfie-success class="success-card" hidden>
                            <div class="success-mark">OK</div>
                            <h3>Photo uploaded successfully</h3>
                            <p>Ready to choose your style.</p>
                            <button
                                type="button"
                                class="btn btn--primary btn--block"
                                wire:click="nextStep"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="nextStep">Choose style</span>
                                <span wire:loading wire:target="nextStep">Please wait&hellip;</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </section>

    @elseif($step === 'template')
        <section class="wizard-panel wizard-panel--wide" aria-labelledby="template-title">
            @include('livewire.partials.submission-steps', ['step' => $step])

            <div class="page-heading">
                <span class="eyebrow">Step 03</span>
                <h2 id="template-title">Choose your Saudi style</h2>
                <p>Select the visual world you want for your AI portrait.</p>
            </div>

            <div wire:loading wire:target="chooseTemplate" class="card success-card">
                <div class="spinner" style="margin-bottom:var(--space-4);"></div>
                <h3>Preparing your generation</h3>
                <p>Please wait while your selected style is queued.</p>
            </div>

            <div wire:loading.remove wire:target="chooseTemplate">
                @if($templates->isEmpty())
                    <div class="card success-card">
                        <p>No styles available right now. Please try again later.</p>
                    </div>
                @else
                    <div class="template-gallery">
                        @foreach($templates as $template)
                            <button
                                type="button"
                                class="card template-card"
                                wire:key="template-{{ $template->id }}"
                                wire:click="chooseTemplate({{ $template->id }})"
                                wire:loading.attr="disabled"
                            >
                                <span class="template-card__image">
                                    <img
                                        src="{{ $template->thumbnailUrl() ?? $template->imageUrl() }}"
                                        alt="{{ $template->title }}"
                                    >
                                </span>
                                <span class="template-card__body">
                                    <span class="template-card__title">{{ $template->title }}</span>
                                    @if($template->description)
                                        <span class="template-card__desc">{{ Str::limit($template->description, 90) }}</span>
                                    @endif
                                    <span class="template-card__action">Generate</span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
