@extends('layouts.public')
@section('title', __('home.title'))

@section('content')
    <section class="hero-panel" style="max-width:860px;margin-inline:auto;">
        <span class="eyebrow" style="color:var(--color-gold-soft);">{{ __('home.title') }}</span>
        <h1 style="margin-top:var(--space-4);">{{ __('home.headline') }}</h1>
        <p>{{ __('home.description') }}</p>
        <a href="{{ route('home') }}" class="btn btn--gold">{{ __('btn.start_now') }}</a>
    </section>
@endsection
