@extends('layouts.admin')
@section('title', 'Submissions')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);">
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Submissions</h1>
    </div>

    @if($submissions->isEmpty())
        <div class="card">
            <p style="color:var(--color-text-dim);font-size:14px;">No submissions yet.</p>
        </div>
    @else
        <div class="card" style="padding:0;overflow:hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                        <tr>
                            <td style="color:var(--color-text-dim);font-size:12px;">{{ $submission->id }}</td>
                            <td>{{ $submission->contact?->name ?? '—' }}</td>
                            <td>{{ $submission->contact?->phone ?? '—' }}</td>
                            <td>{{ $submission->template?->title ?? '—' }}</td>
                            <td><span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">{{ $submission->status->label() }}</span></td>
                            <td style="font-size:13px;color:var(--color-text-dim);">{{ $submission->created_at->format('d M Y H:i') }}</td>
                            <td><a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn--ghost" style="font-size:13px;padding:6px 12px;">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:var(--space-4);">
            {{ $submissions->links() }}
        </div>
    @endif
@endsection
