<div class="steps" aria-label="Submission steps">
    <div class="steps__item {{ $step === 'form' ? 'steps__item--active' : 'steps__item--done' }}">
        <span>1</span>
        Your info
    </div>
    <div class="steps__item {{ $step === 'selfie' ? 'steps__item--active' : ($step === 'template' ? 'steps__item--done' : '') }}">
        <span>2</span>
        Selfie
    </div>
    <div class="steps__item {{ $step === 'template' ? 'steps__item--active' : '' }}">
        <span>3</span>
        Choose style
    </div>
</div>
