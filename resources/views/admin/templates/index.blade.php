@extends('layouts.admin')
@section('title', __('templates.title'))

@section('content')
    <div class="admin-page-header">
        <div>
            <span class="eyebrow">{{ __('templates.library') }}</span>
            <h1 class="admin-page-header__title">{{ __('templates.title') }}</h1>
        </div>
        <a href="{{ route('admin.templates.create') }}" class="btn btn--primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('btn.new_template') }}
        </a>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        @if($templates->isEmpty())
            <div class="table-empty">
                <div style="font-size:32px;margin-bottom:var(--space-3);opacity:0.3;">◆</div>
                {{ __('templates.none') }}
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:76px;">{{ __('table.preview') }}</th>
                            <th>{{ __('table.title_desc') }}</th>
                            <th style="width:80px;text-align:center;">{{ __('table.order') }}</th>
                            <th style="width:100px;text-align:center;">{{ __('table.status') }}</th>
                            <th style="width:140px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            <tr>
                                <td>
                                    <div style="width:52px;height:64px;border-radius:var(--radius);overflow:hidden;border:1px solid var(--color-border);flex-shrink:0;">
                                        <img src="{{ $template->thumbnailUrl() ?? $template->imageUrl() }}"
                                             alt="{{ $template->title }}"
                                             style="width:100%;height:100%;object-fit:cover;display:block;">
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:800;color:var(--color-primary);font-size:14px;">{{ $template->title }}</div>
                                    @if($template->description)
                                        <div style="font-size:12px;color:var(--color-text-muted);margin-top:3px;line-height:1.4;">
                                            {{ Str::limit($template->description, 80) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-size:13px;font-weight:700;color:var(--color-text-muted);">{{ $template->sort_order }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <form method="POST" action="{{ route('admin.templates.toggle', $template) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="badge {{ $template->is_active ? 'badge--completed' : 'badge--cancelled' }}"
                                            style="cursor:pointer;background:none;border:1px solid;padding:5px 10px;font-size:11px;font-weight:700;"
                                            title="{{ __('templates.toggle_hint') }}">
                                            {{ $template->is_active ? __('templates.active') : __('templates.inactive') }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:var(--space-2);justify-content:flex-end;">
                                        <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn--ghost btn--sm">{{ __('btn.edit') }}</a>
                                        <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                                              onsubmit="return confirm('{{ __('templates.confirm_delete') }}');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn--danger btn--sm">{{ __('btn.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
