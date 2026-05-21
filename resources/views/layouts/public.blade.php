@props(['title' => config('app.name')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} &mdash; {{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="public-shell">
    <header class="public-header">
        <a href="{{ route('home') }}" style="font-weight:700;font-size:1.25rem;text-decoration:none;color:var(--color-text);">
            {{ config('app.name') }}
        </a>
    </header>

    <main class="public-main">
        {{ $slot }}
    </main>

    <footer class="public-footer">
        <small style="color:var(--color-text-muted);">&copy; {{ date('Y') }} {{ config('app.name') }}</small>
    </footer>
</body>
</html>
