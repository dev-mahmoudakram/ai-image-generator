<div style="max-width:560px;margin:0 auto;padding:var(--space-6) 0;">

    {{-- Poll while not terminal --}}
    @if(!$submission->status->isTerminal())
        <div wire:poll.2000ms style="display:none;"></div>
    @endif

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);">
        <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">
            {{ $submission->status->label() }}
        </span>
        <span style="font-size:13px;color:var(--color-text-dim);">
            {{ $submission->created_at->diffForHumans() }}
        </span>
    </div>

    {{-- ── Completed ──────────────────────────────────────────────────────────── --}}
    @if($submission->status->value === 'completed' && $submission->generatedImage)
        <div class="card" style="padding:var(--space-4);margin-bottom:var(--space-4);">
            <img
                src="{{ $submission->generatedImage->url() }}"
                alt="Your AI portrait"
                style="width:100%;border-radius:var(--radius-lg);display:block;"
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
        <a href="{{ route('home') }}" class="btn btn--block" style="text-align:center;">
            Create another
        </a>

    {{-- ── Processing / Queued ─────────────────────────────────────────────────── --}}
    @elseif($submission->status->isGenerating())
        <div class="card" style="text-align:center;padding:var(--space-8);">
            <div class="spinner" style="margin-bottom:var(--space-5);"></div>
            <p style="font-weight:600;margin:0 0 var(--space-2);">Generating your portrait&hellip;</p>
            <p style="font-size:14px;color:var(--color-text-dim);margin:0;">
                This usually takes 20–60 seconds. You can safely leave this page and come back using the link below.
            </p>
        </div>

        <div style="margin-top:var(--space-4);padding:var(--space-4);background:var(--color-surface);border-radius:var(--radius);border:1px solid var(--color-border);">
            <p style="font-size:13px;color:var(--color-text-dim);margin:0 0 var(--space-2);">Bookmark this link to check back later:</p>
            <code style="font-size:12px;word-break:break-all;color:var(--color-text);">{{ request()->url() }}</code>
        </div>

    {{-- ── Failed ──────────────────────────────────────────────────────────────── --}}
    @elseif($submission->status->value === 'failed')
        <div class="card" style="text-align:center;padding:var(--space-8);">
            <p style="font-weight:600;color:var(--color-danger);margin:0 0 var(--space-2);">Generation failed</p>
            <p style="font-size:14px;color:var(--color-text-dim);margin:0 0 var(--space-5);">
                Something went wrong. Please try again or contact support.
            </p>
            <a href="{{ route('home') }}" class="btn btn--primary">Try again</a>
        </div>

    {{-- ── Other (queued before isGenerating covers it, or draft, etc.) ───────── --}}
    @else
        <div class="card" style="text-align:center;padding:var(--space-8);">
            <p style="color:var(--color-text-dim);">Waiting to process your submission&hellip;</p>
        </div>
    @endif

</div>

