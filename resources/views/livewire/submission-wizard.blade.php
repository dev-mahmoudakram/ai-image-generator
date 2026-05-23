<div class="experience-shell">
    @if($step === 'form')
        <div class="hero-grid">
            <section class="hero-panel" aria-labelledby="hero-title">
                <span class="eyebrow" style="color:var(--color-gold-soft);">{{ __('home.title') }}</span>
                <h1 id="hero-title" style="margin-top:var(--space-4);">
                    {{ __('home.headline') }}
                </h1>
                <p style="max-width:560px;font-size:1.03rem;margin-top:var(--space-4);">
                    {{ __('home.description') }}
                </p>
                <div class="hero-panel__meta" aria-label="Experience highlights">
                    <span class="hero-chip">{{ __('home.chip_heritage') }}</span>
                    <span class="hero-chip">{{ __('home.chip_mobile') }}</span>
                    <span class="hero-chip">{{ __('home.chip_private') }}</span>
                </div>
            </section>

            <section class="wizard-panel" aria-labelledby="form-title">
                @include('livewire.partials.submission-steps', ['step' => $step])

                <div class="card premium-card">
                    <span class="eyebrow">{{ __('wizard.step_info') }}</span>
                    <h2 id="form-title">{{ __('wizard.step_info_label') }}</h2>
                    <p>{{ __('wizard.step_info_sub') }}</p>

                    <form novalidate x-data x-on:submit.prevent="
                        if (window.__syncPhone) await window.__syncPhone();
                        $wire.submitForm();
                    ">
                        <div class="field">
                            <label class="field__label" for="name">{{ __('wizard.full_name') }}</label>
                            <input
                                class="field__input"
                                type="text"
                                id="name"
                                wire:model.blur="name"
                                autocomplete="name"
                                placeholder="{{ __('wizard.name_placeholder') }}"
                            >
                            @error('name') <p class="field__error">{{ $message }}</p> @enderror
                        </div>

                        <div class="field field--phone">
                            <label class="field__label" for="phone">{{ __('wizard.phone') }}</label>
                            <div x-data="phoneInput" wire:ignore>
                                <input
                                    x-ref="input"
                                    class="field__input"
                                    type="tel"
                                    id="phone"
                                    autocomplete="tel"
                                >
                            </div>
                            @error('phone') <p class="field__error">{{ $message }}</p> @enderror
                        </div>

                        <div class="field">
                            <label class="field__label" for="email">{{ __('wizard.email') }} <span style="font-weight:400;opacity:0.6;">{{ __('wizard.email_optional') }}</span></label>
                            <input
                                class="field__input"
                                type="email"
                                id="email"
                                wire:model.blur="email"
                                autocomplete="email"
                                placeholder="{{ __('wizard.email_placeholder') }}"
                            >
                            @error('email') <p class="field__error">{{ $message }}</p> @enderror
                        </div>

                        <label class="checkbox" style="margin-bottom:var(--space-5);">
                            <input type="checkbox" wire:model="consent">
                            <span>{{ __('wizard.consent') }}</span>
                        </label>
                        @error('consent') <p class="field__error" style="margin-top:calc(-1 * var(--space-3));margin-bottom:var(--space-4);">{{ $message }}</p> @enderror

                        <button type="submit" class="btn btn--primary btn--block" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitForm">{{ __('btn.continue') }}</span>
                            <span wire:loading wire:target="submitForm">{{ __('btn.please_wait') }}</span>
                        </button>
                    </form>
                </div>
            </section>
        </div>

    @elseif($step === 'selfie')
        <section class="wizard-panel" aria-labelledby="selfie-title">
            @include('livewire.partials.submission-steps', ['step' => $step])

            <div class="card premium-card">
                <span class="eyebrow">{{ __('wizard.step_selfie') }}</span>
                <h2 id="selfie-title">{{ __('wizard.step_selfie_label') }}</h2>
                <p>{{ __('wizard.step_selfie_sub') }}</p>

                @if($selfieAttached)
                    <div class="success-card">
                        <div class="success-mark">OK</div>
                        <h3>{{ __('wizard.photo_success') }}</h3>
                        <p>{{ __('wizard.photo_ready') }}</p>
                        <button
                            type="button"
                            class="btn btn--primary btn--block"
                            wire:click="nextStep"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="nextStep">{{ __('btn.choose_style') }}</span>
                            <span wire:loading wire:target="nextStep">{{ __('btn.please_wait') }}&hellip;</span>
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
                                    <span>{{ __('wizard.camera_placeholder') }}</span>
                                </div>

                                <div class="camera__actions">
                                    <button type="button" class="btn btn--primary" data-camera-open>{{ __('btn.open_camera') }}</button>
                                    <button type="button" class="btn btn--primary" data-camera-capture hidden>{{ __('btn.take_photo') }}</button>
                                    <button type="button" class="btn" data-camera-cancel hidden>{{ __('btn.cancel') }}</button>
                                    <button type="button" class="btn" data-camera-retake hidden>{{ __('btn.retake') }}</button>
                                </div>

                                <p data-selfie-status class="camera-note" hidden></p>
                            </div>

                            <div class="upload-divider">
                                <span>{{ __('wizard.upload_or') }}</span>
                            </div>

                            <div
                                class="drop-zone"
                                data-selfie-drop-zone
                                role="button"
                                tabindex="0"
                            >
                                <img
                                    data-selfie-preview
                                    alt="{{ __('wizard.photo_success') }}"
                                    class="drop-zone__img"
                                    hidden
                                >

                                <div class="drop-zone__body">
                                    <svg class="drop-zone__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <p class="drop-zone__title">
                                        {{ __('file_drop.title') }}<br>
                                        <span>{{ __('file_drop.browse') }}</span>
                                    </p>
                                    <p class="drop-zone__hint">{{ __('wizard.file_hint') }}</p>
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
                            <h3>{{ __('wizard.photo_success') }}</h3>
                            <p>{{ __('wizard.photo_ready') }}</p>
                            <button
                                type="button"
                                class="btn btn--primary btn--block"
                                wire:click="nextStep"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="nextStep">{{ __('btn.choose_style') }}</span>
                                <span wire:loading wire:target="nextStep">{{ __('btn.please_wait') }}&hellip;</span>
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
                <span class="eyebrow">{{ __('wizard.step_style') }}</span>
                <h2 id="template-title">{{ __('wizard.step_style_label') }}</h2>
                <p>{{ __('wizard.step_style_sub') }}</p>
            </div>

            <div wire:loading wire:target="chooseTemplate" class="card success-card">
                <div class="spinner" style="margin-bottom:var(--space-4);"></div>
                <h3>{{ __('wizard.preparing') }}</h3>
                <p>{{ __('wizard.please_wait') }}</p>
            </div>

            <div wire:loading.remove wire:target="chooseTemplate">
                @if($templates->isEmpty())
                    <div class="card success-card">
                        <p>{{ __('wizard.no_styles') }}</p>
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
                                    <span class="template-card__action">{{ __('btn.generate') }}</span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
