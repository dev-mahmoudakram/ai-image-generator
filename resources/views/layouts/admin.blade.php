<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') &mdash; {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/admin.js'])
</head>
<body>
<div class="admin-shell">

    {{-- ── Sidebar ────────────────────────────────────────────────────────── --}}
    <aside class="admin-sidebar">
        <div class="admin-sidebar__top">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span class="admin-brand__seal" aria-hidden="true">م</span>
                <span>
                    <span class="admin-brand__name">{{ config('app.name') }}</span>
                    <span class="admin-brand__sub">{{ __('nav.admin_panel') }}</span>
                </span>
            </a>
        </div>

        <nav class="admin-nav" aria-label="{{ __('nav.admin_panel') }}">
            <span class="admin-nav-section">{{ __('nav.main') }}</span>

            <a href="{{ route('admin.dashboard') }}"
               class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'admin-nav-link--active' : '' }}">
                <span class="admin-nav-icon" aria-hidden="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                </span>
                {{ __('nav.dashboard') }}
            </a>

            <a href="{{ route('admin.submissions.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.submissions.*') ? 'admin-nav-link--active' : '' }}">
                <span class="admin-nav-icon" aria-hidden="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4M5 8h14M3 6a1 1 0 011-1h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V6z"/>
                    </svg>
                </span>
                {{ __('nav.submissions') }}
            </a>

            <a href="{{ route('admin.templates.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.templates.*') ? 'admin-nav-link--active' : '' }}">
                <span class="admin-nav-icon" aria-hidden="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path stroke-linecap="round" d="M3 9h18M9 21V9"/>
                    </svg>
                </span>
                {{ __('nav.templates') }}
            </a>
        </nav>

        <div class="admin-sidebar__footer">
            <div class="admin-user-row">
                <span class="admin-user-avatar" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </span>
                <span class="admin-user-name">{{ auth()->user()->name }}</span>
            </div>

            <form method="POST" action="{{ route('locale.switch') }}" style="margin-bottom:var(--space-2);">
                @csrf
                @if(app()->getLocale() === 'ar')
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" class="btn btn--ghost btn--sm" style="width:100%;color:rgba(255,255,255,0.6);border-color:rgba(255,255,255,0.12);">EN</button>
                @else
                    <input type="hidden" name="locale" value="ar">
                    <button type="submit" class="btn btn--ghost btn--sm" style="width:100%;color:rgba(255,255,255,0.6);border-color:rgba(255,255,255,0.12);">ع</button>
                @endif
            </form>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn--ghost btn--sm" style="width:100%;color:rgba(255,255,255,0.6);border-color:rgba(255,255,255,0.12);">
                    {{ __('nav.sign_out') }}
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main content ───────────────────────────────────────────────────── --}}
    <div class="admin-main">
        @if(session('success'))
            <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="admin-alert admin-alert--error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

</div>
</body>
</html>
