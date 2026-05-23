@extends('layouts.admin')
@section('title', __('dashboard.title'))

@section('content')
    <div class="admin-page-header">
        <div>
            <span class="eyebrow">{{ __('dashboard.overview') }}</span>
            <h1 class="admin-page-header__title">{{ __('dashboard.title') }}</h1>
        </div>
        <span style="font-size:13px;color:var(--color-text-muted);align-self:flex-end;">{{ now()->format('l, d M Y') }}</span>
    </div>

    {{-- Stat cards --}}
    <div class="admin-stat-grid">
        <div class="card admin-stat">
            <div class="admin-stat__icon admin-stat__icon--neutral">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h8M4 18h8"/></svg>
            </div>
            <div class="admin-stat__value">{{ $stats['total'] }}</div>
            <div class="admin-stat__label">{{ __('dashboard.total_submissions') }}</div>
        </div>

        <div class="card admin-stat">
            <div class="admin-stat__icon admin-stat__icon--processing">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="admin-stat__value admin-stat__value--processing">{{ $stats['processing'] }}</div>
            <div class="admin-stat__label">{{ __('dashboard.in_queue') }}</div>
        </div>

        <div class="card admin-stat">
            <div class="admin-stat__icon admin-stat__icon--success">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="admin-stat__value admin-stat__value--success">{{ $stats['completed'] }}</div>
            <div class="admin-stat__label">{{ __('dashboard.completed') }}</div>
        </div>

        <div class="card admin-stat">
            <div class="admin-stat__icon admin-stat__icon--danger">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="admin-stat__value admin-stat__value--danger">{{ $stats['failed'] }}</div>
            <div class="admin-stat__label">{{ __('dashboard.failed') }}</div>
        </div>

        <div class="card admin-stat">
            <div class="admin-stat__icon admin-stat__icon--gold">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
            </div>
            <div class="admin-stat__value admin-stat__value--gold">{{ $stats['templates'] }}</div>
            <div class="admin-stat__label">{{ __('dashboard.templates') }}</div>
        </div>
    </div>

    {{-- Recent submissions --}}
    <div class="card" style="padding:0;overflow:hidden;">
        <div class="table-card-header">
            <div>
                <h2 class="table-card-header__title">{{ __('dashboard.recent_submissions') }}</h2>
                <p class="table-card-header__sub">{{ __('dashboard.recent_subtitle') }}</p>
            </div>
            <a href="{{ route('admin.submissions.index') }}" class="btn btn--ghost btn--sm">{{ __('btn.view_all') }}</a>
        </div>

        @if($recent->isEmpty())
            <div class="table-empty">
                <div style="font-size:28px;margin-bottom:12px;opacity:0.2;">◆</div>
                {{ __('dashboard.no_submissions') }}
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('table.name') }}</th>
                            <th>{{ __('table.template') }}</th>
                            <th>{{ __('table.status') }}</th>
                            <th>{{ __('table.submitted') }}</th>
                            <th style="text-align:right;padding-right:24px;">{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $submission)
                            <tr>
                                <td>
                                    <div class="td-person">
                                        <div class="td-person__avatar">{{ mb_substr($submission->contact?->name ?? '?', 0, 1) }}</div>
                                        <div>
                                            <div class="td-person__name">{{ $submission->contact?->name ?? '—' }}</div>
                                            @if($submission->contact?->phone)
                                                <div class="td-person__sub td-mono">{{ $submission->contact->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="td-dim" style="font-size:13px;">{{ $submission->template?->title ?? '—' }}</td>
                                <td>
                                    <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">
                                        {{ $submission->status->label() }}
                                    </span>
                                </td>
                                <td class="td-muted" style="font-size:13px;white-space:nowrap;">{{ $submission->created_at->diffForHumans() }}</td>
                                <td class="td-actions" style="padding-right:24px;">
                                    <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn--ghost btn--sm">{{ __('btn.view') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
