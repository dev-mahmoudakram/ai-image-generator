<div class="status-shell">
    @if(!$submission->status->isTerminal())
        <div wire:poll.2000ms style="display:none;"></div>
    @endif

    <div class="page-heading">
        <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">
            {{ $submission->status->label() }}
        </span>
        <h1 style="margin-top:var(--space-4);">{{ __('status.journey') }}</h1>
        <p>{{ __('status.submitted', ['time' => $submission->created_at->diffForHumans()]) }}</p>
    </div>

    @if($submission->status->value === 'completed' && $submission->generatedImage)
        <div class="card premium-card result-shell">
            <div class="success-card" style="padding-top:0;">
                <div class="success-mark">OK</div>
                <h2>{{ __('status.ready_title') }}</h2>
                <p>{{ __('status.ready_sub') }}</p>
            </div>

            <div class="portrait-frame" style="margin-bottom:var(--space-5);">
                <img
                    src="{{ $submission->generatedImage->url() }}"
                    alt="{{ __('status.ready_title') }}"
                >
            </div>

            <a
                href="{{ $submission->generatedImage->url() }}"
                download="portrait.jpg"
                class="btn btn--primary btn--block"
                style="margin-bottom:var(--space-3);"
            >
                {{ __('btn.download') }}
            </a>
            <a href="{{ route('home') }}" class="btn btn--block">
                {{ __('btn.create_another') }}
            </a>
        </div>

    @elseif($submission->status->isGenerating())
        <div class="card premium-card success-card">
            <div class="processing-orb" aria-hidden="true"></div>
            <h2>{{ __('status.crafting_title') }}</h2>
            <p>{{ __('status.crafting_sub') }}</p>
        </div>

        <div class="code-box" style="margin-top:var(--space-4);">
            <p style="font-size:13px;margin-bottom:var(--space-2);">{{ __('status.bookmark') }}</p>
            <code>{{ route('submission.track', $submission->tracking_token) }}</code>
        </div>

    @elseif($submission->status->value === 'failed')
        <div class="card premium-card success-card">
            <div class="success-mark" style="background:linear-gradient(160deg,var(--color-danger),#7f2927);">!</div>
            <h2>{{ __('status.failed_title') }}</h2>
            <p>{{ __('status.failed_sub') }}</p>
            @if(app()->isLocal() && $submission->latestAttempt?->error_message)
                <div class="code-box" style="margin:var(--space-4) 0;text-align:left;">
                    <p style="font-size:13px;margin-bottom:var(--space-2);">{{ __('status.debug') }}</p>
                    <code>{{ Str::limit($submission->latestAttempt->error_message, 300) }}</code>
                </div>
            @endif
            <a href="{{ route('home') }}" class="btn btn--primary">{{ __('btn.try_again') }}</a>
        </div>

    @elseif($submission->status->value === 'cancelled')
        <div class="card premium-card success-card">
            <div class="success-mark" style="background:linear-gradient(160deg,var(--color-danger),#7f2927);">!</div>
            <h2>{{ __('status.cancelled_title') }}</h2>
            <p>{{ __('status.cancelled_sub') }}</p>
            <a href="{{ route('home') }}" class="btn btn--primary">{{ __('btn.create_another') }}</a>
        </div>

    @else
        <div class="card premium-card success-card">
            <div class="processing-orb" aria-hidden="true"></div>
            <h2>{{ __('status.waiting_title') }}</h2>
            <p>{{ __('status.waiting_sub') }}</p>
        </div>
    @endif
</div>
