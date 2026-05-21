@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} &mdash; {{ config('app.name') }} Admin</title>
    @vite(['resources/scss/app.scss'])
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div style="font-weight:700;font-size:1rem;margin-bottom:var(--space-6);color:var(--color-text);">
                {{ config('app.name') }}
                <span style="font-size:0.7rem;font-weight:400;color:var(--color-text-muted);display:block;">Admin</span>
            </div>

            <nav style="display:flex;flex-direction:column;gap:var(--space-1);">
                <a href="{{ route('admin.dashboard') }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'admin-nav-link--active' : '' }} admin-nav-link">
                    Dashboard
                </a>
                <a href="{{ route('admin.submissions.index') }}"
                   class="{{ request()->routeIs('admin.submissions.*') ? 'admin-nav-link--active' : '' }} admin-nav-link">
                    Submissions
                </a>
                <a href="{{ route('admin.templates.index') }}"
                   class="{{ request()->routeIs('admin.templates.*') ? 'admin-nav-link--active' : '' }} admin-nav-link">
                    Templates
                </a>
            </nav>

            <div style="margin-top:auto;padding-top:var(--space-6);">
                <span style="font-size:0.875rem;color:var(--color-text-muted);display:block;margin-bottom:var(--space-2);">
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--sm btn--ghost" style="width:100%;">Logout</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            @if(session('success'))
                <div class="alert alert--success" style="margin-bottom:var(--space-4);padding:var(--space-3);background:var(--color-success-bg,#d1fae5);border-radius:var(--radius-md);color:var(--color-success,#065f46);">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert--error" style="margin-bottom:var(--space-4);padding:var(--space-3);background:var(--color-error-bg,#fee2e2);border-radius:var(--radius-md);color:var(--color-error,#991b1b);">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</body>
</html>
