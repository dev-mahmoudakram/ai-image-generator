<?php

namespace App\Jobs;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Services\Ai\AiImageGenerationService;
use App\Services\Ai\GenerationRequest;
use App\Services\ImageStorageService;
use App\Services\SubmissionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateSubmissionImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 180;
    public array $backoff = [10, 30, 60];

    public function __construct(public readonly int $submissionId) {}

    public function handle(
        AiImageGenerationService $ai,
        ImageStorageService $storage,
        SubmissionService $submissionService,
    ): void {
        $submission = Submission::with(['selfie', 'template'])->findOrFail($this->submissionId);

        if (! in_array($submission->status, [SubmissionStatus::Queued, SubmissionStatus::Failed])) {
            return;
        }

        $selfie   = $submission->selfie;
        $template = $submission->template;

        if (! $selfie || ! $template) {
            throw new \RuntimeException("Selfie or template missing for submission #{$this->submissionId}");
        }

        $prompt = $ai->buildPrompt($template->prompt_hint ?? '');

        $attempt = $submission->attempts()->create([
            'attempt_no'      => $submission->nextAttemptNo(),
            'status'          => 'processing',
            'provider'        => config('ai.default_for_images', 'gemini'),
            'model'           => config('ai.image_model'),
            'prompt'          => $prompt,
            'selfie_asset_id' => $selfie->id,
            'started_at'      => now(),
        ]);

        $submission->update(['status' => SubmissionStatus::Processing]);
        $submission->logEvent('processing', ['attempt_no' => $attempt->attempt_no]);

        try {
            $result = $ai->generate(new GenerationRequest(
                selfiePath:   $selfie->path,
                selfieDisk:   $selfie->disk,
                templatePath: $template->image_path,
                templateDisk: $template->disk,
                prompt:       $prompt,
            ));

            $generatedAsset = $storage->storeGeneratedImage($submission, $result->response);

            $attempt->update([
                'status'             => 'completed',
                'generated_asset_id' => $generatedAsset->id,
                'completed_at'       => now(),
            ]);

            $submissionService->markCompleted($submission, $generatedAsset);

        } catch (Throwable $e) {
            $attempt->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at'  => now(),
            ]);

            $submissionService->markFailed($submission, $e->getMessage());

            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        $submission = Submission::find($this->submissionId);

        $submission?->logEvent('exhausted', [
            'error'   => $e->getMessage(),
            'max_tries' => $this->tries,
        ]);
    }
}
