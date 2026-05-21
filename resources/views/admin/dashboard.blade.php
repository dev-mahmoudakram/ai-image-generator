@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);">
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Dashboard</h1>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:var(--space-4);margin-bottom:var(--space-8);">
        <div class="card">
            <div style="font-size:2rem;font-weight:700;">{{ $stats['total'] }}</div>
            <div style="color:var(--color-text-dim);font-size:13px;">Total submissions</div>
        </div>
        <div class="card">
            <div style="font-size:2rem;font-weight:700;">{{ $stats['processing'] }}</div>
            <div style="color:var(--color-text-dim);font-size:13px;">In queue / processing</div>
        </div>
        <div class="card">
            <div style="font-size:2rem;font-weight:700;">{{ $stats['completed'] }}</div>
            <div style="color:var(--color-text-dim);font-size:13px;">Completed</div>
        </div>
        <div class="card">
            <div style="font-size:2rem;font-weight:700;">{{ $stats['failed'] }}</div>
            <div style="color:var(--color-text-dim);font-size:13px;">Failed</div>
        </div>
        <div class="card">
            <div style="font-size:2rem;font-weight:700;">{{ $stats['templates'] }}</div>
            <div style="color:var(--color-text-dim);font-size:13px;">Templates</div>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size:1rem;font-weight:600;margin:0 0 var(--space-4);">Recent submissions</h2>
        @if($recent->isEmpty())
            <p style="color:var(--color-text-dim);font-size:14px;">No submissions yet.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent as $submission)
                        <tr>
                            <td>{{ $submission->contact?->name ?? '—' }}</td>
                            <td>{{ $submission->contact?->phone ?? '—' }}</td>
                            <td>{{ $submission->template?->title ?? '—' }}</td>
                            <td><span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">{{ $submission->status->label() }}</span></td>
                            <td style="font-size:13px;color:var(--color-text-dim);">{{ $submission->created_at->diffForHumans() }}</td>
                            <td><a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn--ghost" style="font-size:13px;padding:6px 12px;">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
