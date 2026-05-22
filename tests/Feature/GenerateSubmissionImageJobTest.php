<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Jobs\GenerateSubmissionImageJob;
use App\Models\Contact;
use App\Models\Submission;
use App\Models\Template;
use App\Services\Ai\AiImageGenerationService;
use App\Services\ImageStorageService;
use App\Services\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GenerateSubmissionImageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_failure_marks_submission_failed_without_throwing_for_retry(): void
    {
        config([
            'ai.default_for_images' => 'gemini',
            'ai.image_model'        => 'gemini-2.5-flash-image',
        ]);

        $submission = $this->createReadySubmission();
        $ai = Mockery::mock(AiImageGenerationService::class);

        $ai->shouldReceive('buildPrompt')
            ->once()
            ->with('heritage prompt')
            ->andReturn('built prompt');

        $ai->shouldReceive('generate')
            ->once()
            ->andThrow(new RuntimeException('Application rate limited by AI provider [gemini].'));

        $job = new GenerateSubmissionImageJob($submission->id);

        $this->assertSame(1, $job->tries);

        $job->handle(
            $ai,
            app(ImageStorageService::class),
            app(SubmissionService::class),
        );

        $this->assertSame(SubmissionStatus::Failed, $submission->fresh()->status);
        $this->assertDatabaseHas('generation_attempts', [
            'submission_id' => $submission->id,
            'attempt_no'    => 1,
            'status'        => 'failed',
            'provider'      => 'gemini',
            'model'         => 'gemini-2.5-flash-image',
            'prompt'        => 'built prompt',
            'error_message' => 'Application rate limited by AI provider [gemini].',
        ]);
        $this->assertDatabaseHas('submission_events', [
            'submission_id' => $submission->id,
            'event_type'    => 'failed',
        ]);
    }

    public function test_non_queued_submission_is_ignored(): void
    {
        $submission = $this->createReadySubmission(SubmissionStatus::Failed);
        $ai = Mockery::mock(AiImageGenerationService::class);

        $ai->shouldNotReceive('buildPrompt');
        $ai->shouldNotReceive('generate');

        (new GenerateSubmissionImageJob($submission->id))->handle(
            $ai,
            app(ImageStorageService::class),
            app(SubmissionService::class),
        );

        $this->assertDatabaseMissing('generation_attempts', [
            'submission_id' => $submission->id,
        ]);
        $this->assertSame(SubmissionStatus::Failed, $submission->fresh()->status);
    }

    private function createReadySubmission(SubmissionStatus $status = SubmissionStatus::Queued): Submission
    {
        $contact = Contact::create([
            'name'  => 'Test User',
            'phone' => '+9665'.fake()->unique()->numerify('########'),
            'email' => fake()->safeEmail(),
        ]);

        $template = Template::create([
            'title'       => 'Heritage Portrait',
            'slug'        => Str::slug('Heritage Portrait '.Str::random(8)),
            'description' => 'Premium Saudi heritage style.',
            'disk'        => 'public',
            'image_path'  => 'templates/heritage.jpg',
            'prompt_hint' => 'heritage prompt',
            'is_active'   => true,
        ]);

        $submission = Submission::create([
            'uuid'                 => (string) Str::uuid(),
            'tracking_token'       => Str::random(48),
            'contact_id'           => $contact->id,
            'selected_template_id' => $template->id,
            'status'               => $status,
            'consent_accepted'     => true,
            'consented_at'         => now(),
        ]);

        $submission->assets()->create([
            'kind'       => 'selfie',
            'disk'       => 'public',
            'path'       => 'submissions/test/selfie.jpg',
            'mime_type'  => 'image/jpeg',
            'size_bytes' => 1024,
            'width'      => 640,
            'height'     => 640,
        ]);

        return $submission;
    }
}
