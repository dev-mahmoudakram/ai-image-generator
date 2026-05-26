<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(private SubmissionService $submissionService) {}

    public function index(): View
    {
        $submissions = Submission::with(['contact', 'template'])
            ->latest()
            ->paginate(10);

        return view('admin.submissions.index', compact('submissions'));
    }

    public function show(Submission $submission): View
    {
        $submission->load(['contact', 'template', 'assets', 'attempts', 'events']);

        return view('admin.submissions.show', compact('submission'));
    }

    public function retry(Submission $submission): RedirectResponse
    {
        $this->submissionService->retry($submission);

        return back()->with('success', __('submissions.requeued'));
    }

    public function cancel(Submission $submission): RedirectResponse
    {
        $deletedJobs = $this->submissionService->cancelQueuedGeneration($submission);

        return back()->with('success', __('submissions.cancelled', ['count' => $deletedJobs]));
    }
}
