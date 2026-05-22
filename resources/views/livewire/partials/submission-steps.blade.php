<div class="steps" aria-label="Submission steps">

    <div class="steps__item {{ $step === 'form' ? 'steps__item--active' : 'steps__item--done' }}">
        <div class="steps__circle">{{ $step === 'form' ? '1' : '✓' }}</div>
        <div class="steps__label">Your info</div>
    </div>

    <div class="steps__line {{ in_array($step, ['selfie', 'template']) ? 'steps__line--done' : '' }}"></div>

    <div class="steps__item {{ $step === 'selfie' ? 'steps__item--active' : ($step === 'template' ? 'steps__item--done' : '') }}">
        <div class="steps__circle">{{ $step === 'template' ? '✓' : '2' }}</div>
        <div class="steps__label">Selfie</div>
    </div>

    <div class="steps__line {{ $step === 'template' ? 'steps__line--done' : '' }}"></div>

    <div class="steps__item {{ $step === 'template' ? 'steps__item--active' : '' }}">
        <div class="steps__circle">3</div>
        <div class="steps__label">Choose style</div>
    </div>

</div>
