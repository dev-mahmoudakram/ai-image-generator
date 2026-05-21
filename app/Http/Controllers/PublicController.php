<?php

namespace App\Http\Controllers;

use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function __construct(private SubmissionService $submissionService) {}

    public function generate(): View
    {
        return view('public.generate');
    }

    public function track(string $token): View
    {
        $submission = $this->submissionService->findByToken($token);

        abort_unless($submission, 404);

        $submission->load(['contact', 'template', 'selfie', 'generatedImage', 'latestAttempt']);

        return view('public.track', compact('submission'));
    }
}
