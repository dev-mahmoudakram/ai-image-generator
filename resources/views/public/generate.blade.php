@extends('layouts.public')
@section('title', 'Generate your image')

@section('content')
    <section class="hero-panel" style="max-width:860px;margin-inline:auto;">
        <span class="eyebrow" style="color:var(--color-gold-soft);">Saudi AI Portrait</span>
        <h1 style="margin-top:var(--space-4);">Create your AI portrait</h1>
        <p>Upload a selfie, pick a Saudi-inspired style, and generate a polished portrait in seconds.</p>
        <a href="{{ route('home') }}" class="btn btn--gold">Start now</a>
    </section>
@endsection
