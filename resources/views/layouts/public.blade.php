@props(['title' => trim($__env->yieldContent('title')) ?: config('app.name')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} &mdash; {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="public-shell">

    {{-- <header class="public-header">
        <a href="{{ route('home') }}" class="brand-mark" aria-label="{{ config('app.name') }}">
            <span class="brand-mark__seal" aria-hidden="true">م</span>
            <span>
                {{ config('app.name') }}
                <span class="brand-mark__sub">Saudi Portrait Experience</span>
            </span>
        </a>
    </header> --}}

    <main class="public-main">
        @isset($slot)
            {{ $slot }}
        @endisset
        @yield('content')
    </main>

    <footer class="public-footer">
        <small>&copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Crafted for premium Saudi moments.</small>
    </footer>

</body>
</html>
