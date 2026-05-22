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
use Illuminate\Support\Facades\Artisan;
use Laravel\Ai\Exceptions\RateLimitedException;
use Throwable;

class GenerateSubmissionImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries   = 1;
    public int $timeout = 180;

    public function __construct(public readonly int $submissionId) {}

    public function handle(
        AiImageGenerationService $ai,
        ImageStorageService $storage,
        SubmissionService $submissionService,
    ): void {
        set_time_limit(180);

        $submission = Submission::with(['selfie', 'template'])->findOrFail($this->submissionId);

        if ($submission->status !== SubmissionStatus::Queued) {
            return;
        }

        $selfie   = $submission->selfie;
        $template = $submission->template;

        if (! $selfie || ! $template) {
            $submissionService->markFailed(
                $submission,
                "Selfie or template missing for submission #{$this->submissionId}",
            );

            return;
        }

        $prompt = $ai->buildPrompt($template->prompt_hint ?? '');

        $attempt = $submission->attempts()->create([
            'attempt_no'      => $submission->nextAttemptNo(),
            'status'          => 'processing',
            'provider'        => config('ai.default_for_images', 'gemini'),
            'model'           => config('ai.default_for_images') === 'huggingface'
                                    ? config('ai.huggingface.model')
                                    : config('ai.image_model'),
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

            $generatedAsset = $result->response !== null
                ? $storage->storeGeneratedImage($submission, $result->response)
                : $storage->storeRawImage($submission, $result->imageBytes);

            $attempt->update([
                'status'             => 'completed',
                'generated_asset_id' => $generatedAsset->id,
                'completed_at'       => now(),
            ]);

            $submissionService->markCompleted($submission, $generatedAsset);

        } catch (RateLimitedException $e) {
            $attempt->update([
                'status'        => 'failed',
                'error_message' => 'Quota exceeded — queue worker stopped.',
                'completed_at'  => now(),
            ]);

            $submissionService->markFailed($submission, 'AI provider quota exceeded. Please try again later.');

            // Stop all queue workers so no further jobs burn quota that is already exhausted.
            Artisan::call('queue:restart');

            report($e);
        } catch (Throwable $e) {
            $attempt->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at'  => now(),
            ]);

            $submissionService->markFailed($submission, $e->getMessage());

            report($e);
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
