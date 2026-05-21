@extends('layouts.admin')
@section('title', 'Submission #' . $submission->id)

@section('content')
    <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);">
        <a href="{{ route('admin.submissions.index') }}" class="btn btn--ghost">&larr; Back</a>
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Submission #{{ $submission->id }}</h1>
        <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">{{ $submission->status->label() }}</span>

        @if($submission->status->canRetry())
            <form method="POST" action="{{ route('admin.submissions.retry', $submission) }}" style="margin-left:auto;">
                @csrf
                <button type="submit" class="btn btn--primary" style="font-size:13px;padding:8px 16px;">Retry generation</button>
            </form>
        @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);margin-bottom:var(--space-4);">
        <div class="card">
            <h2 style="font-size:1rem;font-weight:600;margin:0 0 var(--space-3);">Contact</h2>
            <dl style="display:grid;grid-template-columns:auto 1fr;gap:var(--space-1) var(--space-4);">
                <dt style="color:var(--color-text-dim);font-size:13px;">Name</dt>
                <dd style="margin:0;">{{ $submission->contact?->name ?? '—' }}</dd>
                <dt style="color:var(--color-text-dim);font-size:13px;">Phone</dt>
                <dd style="margin:0;">{{ $submission->contact?->phone ?? '—' }}</dd>
                <dt style="color:var(--color-text-dim);font-size:13px;">Email</dt>
                <dd style="margin:0;">{{ $submission->contact?->email ?? '—' }}</dd>
            </dl>
        </div>

        <div class="card">
            <h2 style="font-size:1rem;font-weight:600;margin:0 0 var(--space-3);">Details</h2>
            <dl style="display:grid;grid-template-columns:auto 1fr;gap:var(--space-1) var(--space-4);">
                <dt style="color:var(--color-text-dim);font-size:13px;">Template</dt>
                <dd style="margin:0;">{{ $submission->template?->title ?? '—' }}</dd>
                <dt style="color:var(--color-text-dim);font-size:13px;">Token</dt>
                <dd style="margin:0;font-family:monospace;font-size:11px;word-break:break-all;">{{ $submission->tracking_token }}</dd>
                <dt style="color:var(--color-text-dim);font-size:13px;">IP</dt>
                <dd style="margin:0;">{{ $submission->ip_address ?? '—' }}</dd>
                <dt style="color:var(--color-text-dim);font-size:13px;">Created</dt>
                <dd style="margin:0;">{{ $submission->created_at->format('d M Y H:i:s') }}</dd>
            </dl>
        </div>
    </div>

    @if($submission->generatedImage)
        <div class="card" style="margin-bottom:var(--space-4);">
            <h2 style="font-size:1rem;font-weight:600;margin:0 0 var(--space-3);">Generated image</h2>
            <img src="{{ $submission->generatedImage->url() }}" alt="Generated image"
                 style="max-width:400px;border-radius:var(--radius-lg);">
        </div>
    @endif

    <div class="card" style="margin-bottom:var(--space-4);">
        <h2 style="font-size:1rem;font-weight:600;margin:0 0 var(--space-3);">Generation attempts ({{ $submission->attempts->count() }})</h2>
        @if($submission->attempts->isEmpty())
            <p style="color:var(--color-text-dim);font-size:14px;">None yet.</p>
        @else
            <table class="table">
                <thead>
                    <tr><th>#</th><th>Provider</th><th>Model</th><th>Status</th><th>Error</th><th>Duration</th></tr>
                </thead>
                <tbody>
                    @foreach($submission->attempts as $attempt)
                        <tr>
                            <td>{{ $attempt->attempt_no }}</td>
                            <td>{{ $attempt->provider }}</td>
                            <td style="font-size:12px;">{{ $attempt->model }}</td>
                            <td><span class="badge badge--{{ $attempt->status }}">{{ $attempt->status }}</span></td>
                            <td style="font-size:12px;color:var(--color-danger);">{{ $attempt->error_message ?? '—' }}</td>
                            <td style="font-size:13px;">
                                @if($attempt->started_at && $attempt->completed_at)
                                    {{ $attempt->started_at->diffInSeconds($attempt->completed_at) }}s
                                @else —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <h2 style="font-size:1rem;font-weight:600;margin:0 0 var(--space-3);">Event log</h2>
        @if($submission->events->isEmpty())
            <p style="color:var(--color-text-dim);font-size:14px;">No events.</p>
        @else
            <table class="table">
                <thead>
                    <tr><th>Event</th><th>Payload</th><th>Time</th></tr>
                </thead>
                <tbody>
                    @foreach($submission->events->sortBy('created_at') as $event)
                        <tr>
                            <td style="font-family:monospace;font-size:13px;">{{ $event->event_type }}</td>
                            <td style="font-size:12px;color:var(--color-text-dim);">
                                {{ $event->payload ? json_encode($event->payload) : '—' }}
                            </td>
                            <td style="font-size:13px;color:var(--color-text-dim);">{{ $event->created_at->format('H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
