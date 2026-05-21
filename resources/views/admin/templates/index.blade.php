@extends('layouts.admin')
@section('title', 'Templates')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);">
        <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Templates</h1>
        <a href="{{ route('admin.templates.create') }}" class="btn btn--primary" style="font-size:13px;padding:8px 16px;">+ New template</a>
    </div>

    @if($templates->isEmpty())
        <div class="card">
            <p style="color:var(--color-text-dim);font-size:14px;">No templates yet.</p>
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
                                     style="width:48px;height:48px;object-fit:cover;border-radius:var(--radius);">
                            </td>
                            <td>
                                <div style="font-weight:500;">{{ $template->title }}</div>
                                @if($template->description)
                                    <div style="font-size:12px;color:var(--color-text-dim);">{{ Str::limit($template->description, 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $template->sort_order }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.templates.toggle', $template) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn--ghost" style="font-size:12px;padding:4px 10px;">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div style="display:flex;gap:var(--space-2);">
                                    <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn--ghost" style="font-size:13px;padding:6px 12px;">Edit</a>
                                    <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                                          onsubmit="return confirm('Delete this template?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn--ghost" style="font-size:13px;padding:6px 12px;color:var(--color-danger);">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
