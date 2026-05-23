@extends('layouts.admin')
@section('title', __('submissions.title'))

@section('content')
    <div class="admin-page-header">
        <div>
            <span class="eyebrow">{{ __('submissions.operations') }}</span>
            <h1 class="admin-page-header__title">{{ __('submissions.title') }}</h1>
        </div>
        <span style="font-size:13px;color:var(--color-text-muted);">{{ $submissions->total() }}</span>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        @if($submissions->isEmpty())
            <div class="table-empty">
                <div style="font-size:28px;margin-bottom:12px;opacity:0.2;">◆</div>
                {{ __('submissions.none') }}
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:56px;">{{ __('table.hash') }}</th>
                            <th>{{ __('table.name') }}</th>
                            <th>{{ __('table.phone') }}</th>
                            <th>{{ __('table.template') }}</th>
                            <th>{{ __('table.status') }}</th>
                            <th>{{ __('table.date') }}</th>
                            <th style="width:148px;text-align:right;padding-right:24px;">{{ __('table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr>
                                <td>
                                    <span class="td-id">{{ $submission->id }}</span>
                                </td>
                                <td>
                                    <div class="td-person">
                                        <div class="td-person__avatar">{{ mb_substr($submission->contact?->name ?? '?', 0, 1) }}</div>
                                        <span class="td-person__name">{{ $submission->contact?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="td-mono td-muted">{{ $submission->contact?->phone ?? '—' }}</span>
                                </td>
                                <td class="td-dim" style="font-size:13px;">
                                    {{ $submission->template?->title ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge badge--{{ str_replace('_', '-', $submission->status->value) }}">
                                        {{ $submission->status->label() }}
                                    </span>
                                </td>
                                <td class="td-muted" style="font-size:12px;white-space:nowrap;line-height:1.7;">
                                    {{ $submission->created_at->format('d M Y') }}<br>
                                    <span style="font-size:11px;opacity:0.7;">{{ $submission->created_at->format('H:i') }}</span>
                                </td>
                                <td class="td-actions" style="padding-right:24px;">
                                    <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                        @if($submission->status->canCancelQueued())
                                            <form method="POST" action="{{ route('admin.submissions.cancel', $submission) }}"
                                                  onsubmit="return confirm('{{ __('submissions.confirm_cancel') }}');">
                                                @csrf
                                                <button type="submit" class="btn btn--danger btn--sm">{{ __('btn.cancel') }}</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn--ghost btn--sm">{{ __('btn.view') }}</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{ $submissions->links() }}
@endsection
