<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; {{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss'])
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--color-bg);">
    <div class="card" style="width:100%;max-width:400px;margin:var(--space-5);">
        <div style="margin-bottom:var(--space-6);">
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 var(--space-1);">{{ config('app.name') }}</h1>
            <p style="color:var(--color-text-dim);margin:0;font-size:14px;">Admin sign in</p>
        </div>

        @if($errors->any())
            <div style="padding:var(--space-3);background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:var(--radius);color:var(--color-danger);margin-bottom:var(--space-4);font-size:14px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="field">
                <label class="field__label" for="email">Email</label>
                <input
                    class="field__input"
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div class="field">
                <label class="field__label" for="password">Password</label>
                <input
                    class="field__input"
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <label class="checkbox" style="margin-bottom:var(--space-5);">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            <button type="submit" class="btn btn--primary btn--block">Sign in</button>
        </form>
    </div>
</body>
</html>
