<div class="status-shell">
    @if(!$submission->status->isTerminal())
        <div wire:poll.2000ms style="display:none;"></div>
    @endif

    <div class="page-heading">
        <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">
            {{ $submission->status->label() }}
        </span>
        <h1 style="margin-top:var(--space-4);">Your portrait journey</h1>
        <p>Submitted {{ $submission->created_at->diffForHumans() }}.</p>
    </div>

    @if($submission->status->value === 'completed' && $submission->generatedImage)
        <div class="card premium-card result-shell">
            <div class="success-card" style="padding-top:0;">
                <div class="success-mark">OK</div>
                <h2>Your portrait is ready</h2>
                <p>Download and save your Saudi-inspired AI portrait.</p>
            </div>

            <div class="portrait-frame" style="margin-bottom:var(--space-5);">
                <img
                    src="{{ $submission->generatedImage->url() }}"
                    alt="Your AI portrait"
                >
            </div>

            <a
                href="{{ $submission->generatedImage->url() }}"
                download="portrait.jpg"
                class="btn btn--primary btn--block"
                style="margin-bottom:var(--space-3);"
            >
                Download your portrait
            </a>
            <a href="{{ route('home') }}" class="btn btn--block">
                Create another
            </a>
        </div>

    @elseif($submission->status->isGenerating())
        <div class="card premium-card success-card">
            <div class="processing-orb" aria-hidden="true"></div>
            <h2>Crafting your portrait</h2>
            <p>This usually takes 20-60 seconds. You can keep this page open while the result is prepared.</p>
        </div>

        <div class="code-box" style="margin-top:var(--space-4);">
            <p style="font-size:13px;margin-bottom:var(--space-2);">Bookmark this link to check back later:</p>
            <code>{{ route('submission.track', $submission->tracking_token) }}</code>
        </div>

    @elseif($submission->status->value === 'failed')
        <div class="card premium-card success-card">
            <div class="success-mark" style="background:linear-gradient(160deg,var(--color-danger),#7f2927);">!</div>
            <h2>Generation failed</h2>
            <p>Something went wrong. Please try again or contact support.</p>
            @if(app()->isLocal() && $submission->latestAttempt?->error_message)
                <div class="code-box" style="margin:var(--space-4) 0;text-align:left;">
                    <p style="font-size:13px;margin-bottom:var(--space-2);">Local debug detail:</p>
                    <code>{{ Str::limit($submission->latestAttempt->error_message, 300) }}</code>
                </div>
            @endif
            <a href="{{ route('home') }}" class="btn btn--primary">Try again</a>
        </div>

    @elseif($submission->status->value === 'cancelled')
        <div class="card premium-card success-card">
            <div class="success-mark" style="background:linear-gradient(160deg,var(--color-danger),#7f2927);">!</div>
            <h2>Generation cancelled</h2>
            <p>This request was cancelled before the queued job was processed.</p>
            <a href="{{ route('home') }}" class="btn btn--primary">Create another</a>
        </div>

    @else
        <div class="card premium-card success-card">
            <div class="processing-orb" aria-hidden="true"></div>
            <h2>Waiting to process</h2>
            <p>Your request is ready and will move forward shortly.</p>
        </div>
    @endif
</div>
