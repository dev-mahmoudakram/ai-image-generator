<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Jobs\GenerateSubmissionImageJob;
use App\Models\Contact;
use App\Models\Submission;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminSubmissionCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_cancel_a_queued_submission_job(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $submission = $this->createSubmission(SubmissionStatus::Queued);
        $otherSubmission = $this->createSubmission(SubmissionStatus::Queued);

        $jobId = $this->insertQueuedGenerationJob($submission);
        $otherJobId = $this->insertQueuedGenerationJob($otherSubmission);

        $this->actingAs($admin)
            ->post(route('admin.submissions.cancel', $submission))
            ->assertRedirect();

        $this->assertDatabaseMissing('jobs', ['id' => $jobId]);
        $this->assertDatabaseHas('jobs', ['id' => $otherJobId]);
        $this->assertSame(SubmissionStatus::Cancelled, $submission->fresh()->status);
        $this->assertDatabaseHas('submission_events', [
            'submission_id' => $submission->id,
            'event_type'    => 'cancelled',
        ]);
    }

    public function test_admin_cannot_cancel_a_non_queued_submission(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $submission = $this->createSubmission(SubmissionStatus::SelfieUploaded);

        $this->actingAs($admin)
            ->post(route('admin.submissions.cancel', $submission))
            ->assertStatus(422);

        $this->assertSame(SubmissionStatus::SelfieUploaded, $submission->fresh()->status);
        $this->assertDatabaseMissing('submission_events', [
            'submission_id' => $submission->id,
            'event_type'    => 'cancelled',
        ]);
    }

    private function createSubmission(SubmissionStatus $status): Submission
    {
        $contact = Contact::create([
            'name'  => fake()->name(),
            'phone' => '+9665'.fake()->unique()->numerify('########'),
            'email' => fake()->safeEmail(),
        ]);

        $template = Template::create([
            'title'       => fake()->words(2, true),
            'slug'        => Str::slug(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(),
            'disk'        => 'public',
            'image_path'  => 'templates/test.jpg',
            'is_active'   => true,
        ]);

        return Submission::create([
            'uuid'                 => (string) Str::uuid(),
            'tracking_token'       => Str::random(48),
            'contact_id'           => $contact->id,
            'selected_template_id' => $template->id,
            'status'               => $status,
            'consent_accepted'     => true,
            'consented_at'         => now(),
        ]);
    }

    private function insertQueuedGenerationJob(Submission $submission): int
    {
        $now = now()->timestamp;

        return DB::table('jobs')->insertGetId([
            'queue'        => 'default',
            'payload'      => json_encode([
                'uuid'        => (string) Str::uuid(),
                'displayName' => GenerateSubmissionImageJob::class,
                'job'         => 'Illuminate\Queue\CallQueuedHandler@call',
                'maxTries'    => 1,
                'timeout'     => 180,
                'data'        => [
                    'commandName' => GenerateSubmissionImageJob::class,
                    'command'     => serialize(new GenerateSubmissionImageJob($submission->id)),
                ],
            ]),
            'attempts'     => 0,
            'reserved_at'  => null,
            'available_at' => $now,
            'created_at'   => $now,
        ]);
    }
}
