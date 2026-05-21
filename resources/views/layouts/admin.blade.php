<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') &mdash; {{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss'])
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div style="font-weight:700;font-size:1rem;margin-bottom:var(--space-6);color:var(--color-text);">
                {{ config('app.name') }}
                <span style="font-size:11px;font-weight:400;color:var(--color-text-dim);display:block;">Admin</span>
            </div>

            <nav style="display:flex;flex-direction:column;gap:var(--space-1);">
                <a href="{{ route('admin.dashboard') }}"
                   class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'admin-nav-link--active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.submissions.index') }}"
                   class="admin-nav-link {{ request()->routeIs('admin.submissions.*') ? 'admin-nav-link--active' : '' }}">
                    Submissions
                </a>
                <a href="{{ route('admin.templates.index') }}"
                   class="admin-nav-link {{ request()->routeIs('admin.templates.*') ? 'admin-nav-link--active' : '' }}">
                    Templates
                </a>
            </nav>

            <div style="margin-top:auto;padding-top:var(--space-6);">
                <span style="font-size:13px;color:var(--color-text-dim);display:block;margin-bottom:var(--space-2);">
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--ghost" style="width:100%;font-size:13px;padding:8px 12px;">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            @if(session('success'))
                <div style="padding:var(--space-3);background:rgba(31,191,117,0.1);border:1px solid rgba(31,191,117,0.3);border-radius:var(--radius);color:var(--color-success);margin-bottom:var(--space-4);font-size:14px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="padding:var(--space-3);background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:var(--radius);color:var(--color-danger);margin-bottom:var(--space-4);font-size:14px;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
