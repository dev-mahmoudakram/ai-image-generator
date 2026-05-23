@props(['title' => trim($__env->yieldContent('title')) ?: config('app.name')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
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

    <div class="locale-switcher">
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf
            @if(app()->getLocale() === 'ar')
                <input type="hidden" name="locale" value="en">
                <button type="submit" class="locale-btn">English</button>
            @else
                <input type="hidden" name="locale" value="ar">
                <button type="submit" class="locale-btn">العربية</button>
            @endif
        </form>
    </div>

    {{-- <header class="public-header">
        <a href="{{ route('home') }}" class="brand-mark" aria-label="{{ config('app.name') }}">
            <span class="brand-mark__seal" aria-hidden="true">م</span>
            <span>
                {{ config('app.name') }}
                <span class="brand-mark__sub">{{ __('public.app_subtitle') }}</span>
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
        <small>&copy; {{ date('Y') }} {{ config('app.name') }} &mdash; {{ __('public.footer') }}</small>

    </footer>

</body>
</html>
