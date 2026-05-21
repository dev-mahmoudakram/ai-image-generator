<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; {{ config('app.name') }} Admin</title>
    @vite(['resources/scss/app.scss'])
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--color-bg);">
    <div class="card" style="width:100%;max-width:400px;margin:var(--space-4);">
        <div style="margin-bottom:var(--space-6);">
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 var(--space-1);">{{ config('app.name') }}</h1>
            <p style="color:var(--color-text-muted);margin:0;font-size:0.875rem;">Admin sign in</p>
        </div>

        @if($errors->any())
            <div style="padding:var(--space-3);background:var(--color-error-bg,#fee2e2);border-radius:var(--radius-md);color:var(--color-error,#991b1b);margin-bottom:var(--space-4);font-size:0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input
                    class="form-input"
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    class="form-input"
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div style="display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-5);">
                <input type="checkbox" id="remember" name="remember" style="width:1rem;height:1rem;">
                <label for="remember" style="font-size:0.875rem;color:var(--color-text-muted);">Remember me</label>
            </div>

            <button type="submit" class="btn btn--primary" style="width:100%;">Sign in</button>
        </form>
    </div>
</body>
</html>
