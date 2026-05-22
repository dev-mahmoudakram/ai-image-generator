@extends('layouts.admin')
@section('title', 'Submission #' . $submission->id)

@section('content')

    {{-- ── Page header ─────────────────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-4);margin-bottom:var(--space-5);flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:var(--space-4);flex-wrap:wrap;">
            <a href="{{ route('admin.submissions.index') }}" class="btn btn--ghost btn--sm">&larr; Back</a>
            <div>
                <span class="eyebrow">Submission</span>
                <div style="display:flex;align-items:center;gap:var(--space-3);margin-top:4px;">
                    <h1 style="margin:0;font-size:2rem;font-weight:900;letter-spacing:-0.04em;line-height:1;color:var(--color-text);">#{{ $submission->id }}</h1>
                    <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">{{ $submission->status->label() }}</span>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:var(--space-2);align-items:center;flex-wrap:wrap;">
            @if($submission->status->canCancelQueued())
                <form method="POST" action="{{ route('admin.submissions.cancel', $submission) }}"
                      onsubmit="return confirm('Cancel this queued generation?');">
                    @csrf
                    <button type="submit" class="btn btn--danger">Cancel job</button>
                </form>
            @endif
            @if($submission->status->canRetry())
                <form method="POST" action="{{ route('admin.submissions.retry', $submission) }}">
                    @csrf
                    <button type="submit" class="btn btn--primary">Retry generation</button>
                </form>
            @endif
        </div>
    </div>

    {{-- ── Quick status bar ────────────────────────────────────────────────── --}}
    <div class="sub-status-bar">
        <div class="sub-status-bar__item">
            <div class="sub-status-bar__label">Status</div>
            <div class="sub-status-bar__value">{{ $submission->status->label() }}</div>
        </div>
        <div class="sub-status-bar__item">
            <div class="sub-status-bar__label">Template</div>
            <div class="sub-status-bar__value">{{ $submission->template?->title ?? '—' }}</div>
        </div>
        <div class="sub-status-bar__item">
            <div class="sub-status-bar__label">Submitted</div>
            <div class="sub-status-bar__value">{{ $submission->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div class="sub-status-bar__item">
            <div class="sub-status-bar__label">Attempts</div>
            <div class="sub-status-bar__value">{{ $submission->attempts->count() }}</div>
        </div>
    </div>

    {{-- ── Contact + Details ───────────────────────────────────────────────── --}}
    <div class="admin-detail-grid">

        {{-- Contact --}}
        <div class="card">
            <div class="contact-hero">
                <div class="contact-hero__avatar">{{ mb_substr($submission->contact?->name ?? '?', 0, 1) }}</div>
                <div>
                    <div class="contact-hero__name">{{ $submission->contact?->name ?? 'Unknown' }}</div>
                    @if($submission->contact?->phone)
                        <div class="contact-hero__sub">{{ $submission->contact->phone }}</div>
                    @endif
                </div>
            </div>

            <div>
                <div class="detail-row">
                    <span class="detail-row__label">Name</span>
                    <span class="detail-row__value">{{ $submission->contact?->name ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Phone</span>
                    <span class="detail-row__value td-mono" style="font-size:13px;">{{ $submission->contact?->phone ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Email</span>
                    <span class="detail-row__value" style="color:var(--color-text-dim);">{{ $submission->contact?->email ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Submission details --}}
        <div class="card">
            <div class="card-section-title">Submission details</div>

            <div>
                <div class="detail-row">
                    <span class="detail-row__label">Template</span>
                    <span class="detail-row__value">{{ $submission->template?->title ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">IP</span>
                    <span class="detail-row__value td-mono" style="font-size:13px;">{{ $submission->ip_address ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Created</span>
                    <span class="detail-row__value" style="font-size:13px;">{{ $submission->created_at->format('d M Y, H:i:s') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Token</span>
                    <span class="detail-row__value" style="font-family:'Fira Code','Cascadia Code',monospace;font-size:10.5px;color:var(--color-text-muted);word-break:break-all;">{{ $submission->tracking_token }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Generated image ─────────────────────────────────────────────────── --}}
    @if($submission->generatedImage)
        <div class="card" style="margin-bottom:var(--space-5);">
            <div class="card-section-title">Generated portrait</div>
            <div class="portrait-frame" style="max-width:360px;">
                <img src="{{ $submission->generatedImage->url() }}" alt="Generated portrait">
            </div>
        </div>
    @endif

    {{-- ── Generation attempts ─────────────────────────────────────────────── --}}
    <div class="card" style="padding:0;overflow:hidden;margin-bottom:var(--space-5);">
        <div class="table-card-header">
            <div>
                <h2 class="table-card-header__title">Generation attempts</h2>
                <p class="table-card-header__sub">{{ $submission->attempts->count() }} attempt(s) recorded</p>
            </div>
        </div>

        @if($submission->attempts->isEmpty())
            <div class="table-empty">
                <div style="font-size:28px;margin-bottom:12px;opacity:0.2;">◆</div>
                No generation attempts yet.
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:48px;">#</th>
                            <th>Provider</th>
                            <th>Model</th>
                            <th>Status</th>
                            <th>Error</th>
                            <th style="width:88px;">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submission->attempts as $attempt)
                            <tr>
                                <td><span class="td-id">{{ $attempt->attempt_no }}</span></td>
                                <td class="td-dim" style="font-size:13px;">{{ $attempt->provider }}</td>
                                <td><span class="td-mono" style="font-size:12px;color:var(--color-text-muted);">{{ $attempt->model }}</span></td>
                                <td><span class="badge badge--{{ $attempt->status }}">{{ $attempt->status }}</span></td>
                                <td style="font-size:12px;color:var(--color-danger);max-width:240px;">{{ $attempt->error_message ?? '—' }}</td>
                                <td class="td-muted" style="font-size:13px;font-variant-numeric:tabular-nums;">
                                    @if($attempt->started_at && $attempt->completed_at)
                                        {{ $attempt->started_at->diffInSeconds($attempt->completed_at) }}s
                                    @else —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Event log ───────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-section-title">Event log</div>

        @if($submission->events->isEmpty())
            <p style="color:var(--color-text-muted);font-size:14px;margin:0;">No events recorded.</p>
        @else
            <div class="event-timeline">
                @foreach($submission->events->sortBy('id') as $event)
                    <div class="event-timeline__item">
                        <div class="event-timeline__dot"></div>
                        <div class="event-timeline__body">
                            <div class="event-timeline__type">{{ $event->event_type }}</div>
                            @if($event->payload)
                                <div class="event-timeline__payload">{{ json_encode($event->payload) }}</div>
                            @endif
                        </div>
                        <div class="event-timeline__time">{{ $event->created_at->format('H:i:s') }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
