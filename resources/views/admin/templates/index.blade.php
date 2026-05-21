<x-layouts.admin title="Templates">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);">
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Templates</h1>
        <a href="{{ route('admin.templates.create') }}" class="btn btn--primary btn--sm">+ New template</a>
    </div>

    @if($templates->isEmpty())
        <div class="card">
            <p style="color:var(--color-text-muted);font-size:0.875rem;">No templates yet.</p>
        </div>
    @else
        <div class="card" style="padding:0;overflow:hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td style="width:64px;">
                                <img src="{{ $template->thumbnailUrl() ?? $template->imageUrl() }}"
                                     alt="{{ $template->title }}"
                                     style="width:48px;height:48px;object-fit:cover;border-radius:var(--radius-sm);">
                            </td>
                            <td>
                                <div style="font-weight:500;">{{ $template->title }}</div>
                                @if($template->description)
                                    <div style="font-size:0.75rem;color:var(--color-text-muted);">{{ Str::limit($template->description, 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $template->sort_order }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.templates.toggle', $template) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn--sm btn--ghost">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td style="display:flex;gap:var(--space-2);">
                                <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn--sm btn--ghost">Edit</a>
                                <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                                      onsubmit="return confirm('Delete this template?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn--sm btn--ghost" style="color:var(--color-error,#991b1b);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.admin>
