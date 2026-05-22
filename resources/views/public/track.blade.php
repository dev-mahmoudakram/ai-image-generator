@extends('layouts.public')
@section('title', 'Your submission')

@section('content')
    <div class="status-shell">
        <div class="page-heading">
            <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">
                {{ $submission->status->label() }}
            </span>
            <h1 style="margin-top:var(--space-4);">Submission status</h1>
        </div>

        @if($submission->status->value === 'completed' && $submission->generatedImage)
            <div class="card premium-card">
                <div class="portrait-frame" style="margin-bottom:var(--space-5);">
                    <img src="{{ $submission->generatedImage->url() }}"
                         alt="Your AI portrait">
                </div>
                <a href="{{ $submission->generatedImage->url() }}"
                   download
                   class="btn btn--primary btn--block">
                    Download image
                </a>
            </div>
        @elseif($submission->status->isGenerating())
            <div class="card premium-card success-card">
                <div class="processing-orb" aria-hidden="true"></div>
                <p>Your image is being generated. This usually takes under a minute.</p>
            </div>
        @elseif($submission->status->value === 'failed')
            <div class="card premium-card success-card">
                <p style="color:var(--color-danger);font-weight:800;">Generation failed. Please try again or contact support.</p>
            </div>
        @else
            <div class="card premium-card success-card">
                <p>Waiting to process...</p>
            </div>
        @endif
    </div>
@endsection
