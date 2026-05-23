<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.sign_in_title') }} &mdash; {{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss'])
</head>
<body class="public-shell" style="display:grid;place-items:center;min-height:100vh;padding:var(--space-5);">

    <div style="width:100%;max-width:420px;">

        {{-- Brand --}}
        <div style="text-align:center;margin-bottom:var(--space-6);">
            <div style="display:inline-grid;place-items:center;width:52px;height:52px;border-radius:50%;background:linear-gradient(145deg,var(--color-primary),var(--color-primary-2));color:var(--color-gold-soft);font-size:20px;font-weight:900;box-shadow:var(--shadow-lg);margin-bottom:var(--space-4);">م</div>
            <h1 style="font-size:1.6rem;margin-bottom:var(--space-2);">{{ config('app.name') }}</h1>
            <p style="font-size:14px;color:var(--color-text-muted);margin:0;">{{ __('admin.sign_in_subtitle') }}</p>
        </div>

        <div class="card premium-card">
            @if($errors->any())
                <div class="admin-alert admin-alert--error" style="margin-bottom:var(--space-5);">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="field">
                    <label class="field__label" for="email">{{ __('admin.email') }}</label>
                    <input
                        class="field__input"
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="{{ __('admin.email_placeholder') }}"
                    >
                </div>

                <div class="field">
                    <label class="field__label" for="password">{{ __('admin.password') }}</label>
                    <input
                        class="field__input"
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                    >
                </div>

                <label class="checkbox" style="margin-bottom:var(--space-5);">
                    <input type="checkbox" name="remember">
                    <span>{{ __('admin.remember') }}</span>
                </label>

                <button type="submit" class="btn btn--primary btn--block">{{ __('btn.sign_in') }}</button>
            </form>
        </div>

        <p style="text-align:center;font-size:12px;color:var(--color-text-muted);margin-top:var(--space-4);">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>

</body>
</html>
