<x-layouts.admin title="Submissions">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);">
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Submissions</h1>
    </div>

    @if($submissions->isEmpty())
        <div class="card">
            <p style="color:var(--color-text-muted);font-size:0.875rem);">No submissions yet.</p>
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
                            <td style="color:var(--color-text-muted);font-size:0.75rem;">{{ $submission->id }}</td>
                            <td>{{ $submission->contact?->name ?? '—' }}</td>
                            <td>{{ $submission->contact?->phone ?? '—' }}</td>
                            <td>{{ $submission->template?->title ?? '—' }}</td>
                            <td><span class="status-badge status-badge--{{ $submission->status->value }}">{{ $submission->status->label() }}</span></td>
                            <td style="font-size:0.875rem;color:var(--color-text-muted);">{{ $submission->created_at->format('d M Y H:i') }}</td>
                            <td><a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn--sm btn--ghost">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:var(--space-4);">
            {{ $submissions->links() }}
        </div>
    @endif
</x-layouts.admin>
