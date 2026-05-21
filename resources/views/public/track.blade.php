<x-layouts.public title="Your submission">
    <div style="max-width:560px;margin:0 auto;padding:var(--space-8) 0;">
        <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 var(--space-2);">Submission status</h1>

        <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);">
            <span class="status-badge status-badge--{{ $submission->status->value }}">
                {{ $submission->status->label() }}
            </span>
            <span style="color:var(--color-text-muted);font-size:0.875rem;">
                {{ $submission->created_at->diffForHumans() }}
            </span>
        </div>

        @if($submission->status->value === 'completed' && $submission->generatedImage)
            <div class="card" style="margin-bottom:var(--space-4);">
                <img src="{{ $submission->generatedImage->url() }}"
                     alt="Your AI portrait"
                     style="width:100%;border-radius:var(--radius-md);">
                <a href="{{ $submission->generatedImage->url() }}"
                   download
                   class="btn btn--primary"
                   style="margin-top:var(--space-4);display:block;text-align:center;">
                    Download image
                </a>
            </div>
        @elseif($submission->status->isGenerating())
            <div class="card" style="text-align:center;padding:var(--space-8);">
                <p style="color:var(--color-text-muted);">Your image is being generated. This usually takes under a minute.</p>
            </div>
        @elseif($submission->status->value === 'failed')
            <div class="card" style="text-align:center;padding:var(--space-8);">
                <p style="color:var(--color-error,#991b1b);">Generation failed. Please try again or contact support.</p>
            </div>
        @else
            <div class="card" style="text-align:center;padding:var(--space-8);">
                <p style="color:var(--color-text-muted);">Waiting to process...</p>
            </div>
        @endif
    </div>
</x-layouts.public>
