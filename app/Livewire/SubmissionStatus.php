<?php

namespace App\Livewire;

use App\Models\Submission;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class SubmissionStatus extends Component
{
    public string $token;

    public function mount(string $token): void
    {
        Submission::where('tracking_token', $token)->firstOrFail();
        $this->token = $token;
    }

    public function render(): View
    {
        $submission = Submission::where('tracking_token', $this->token)
            ->with(['contact', 'template', 'generatedImage', 'latestAttempt'])
            ->firstOrFail();

        return view('livewire.submission-status', compact('submission'));
    }
}
