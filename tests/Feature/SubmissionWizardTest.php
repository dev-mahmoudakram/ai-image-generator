<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Livewire\SubmissionWizard;
use App\Models\Contact;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_selfie_upload_attaches_asset_to_submission(): void
    {
        Storage::fake('public');

        $submission = $this->createSubmission();

        Livewire::test(SubmissionWizard::class)
            ->set('step', 'selfie')
            ->set('submissionId', $submission->id)
            ->set('submissionToken', $submission->tracking_token)
            ->set('selfie', UploadedFile::fake()->image('selfie.jpg', 640, 640))
            ->assertHasNoErrors('selfie')
            ->assertSet('selfieAttached', true);

        $submission->refresh();

        $this->assertSame(SubmissionStatus::SelfieUploaded, $submission->status);
        $this->assertDatabaseHas('submission_assets', [
            'submission_id' => $submission->id,
            'kind'          => 'selfie',
            'disk'          => 'public',
            'path'          => "submissions/{$submission->uuid}/selfie.jpg",
        ]);
        Storage::disk('public')->assertExists("submissions/{$submission->uuid}/selfie.jpg");
    }

    public function test_invalid_selfie_upload_is_rejected(): void
    {
        Storage::fake('public');

        $submission = $this->createSubmission();

        Livewire::test(SubmissionWizard::class)
            ->set('step', 'selfie')
            ->set('submissionId', $submission->id)
            ->set('submissionToken', $submission->tracking_token)
            ->set('selfie', UploadedFile::fake()->create('selfie.pdf', 10, 'application/pdf'))
            ->assertHasErrors(['selfie' => 'image'])
            ->assertSet('selfieAttached', false);

        $submission->refresh();

        $this->assertSame(SubmissionStatus::FormCompleted, $submission->status);
        $this->assertDatabaseMissing('submission_assets', [
            'submission_id' => $submission->id,
            'kind'          => 'selfie',
        ]);
    }

    public function test_plain_javascript_selfie_endpoint_uploads_image(): void
    {
        Storage::fake('public');

        $submission = $this->createSubmission();

        $this->post(route('submission.selfie.store', $submission->tracking_token), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg', 640, 640),
        ], [
            'Accept' => 'application/json',
        ])
            ->assertOk()
            ->assertJson(['uploaded' => true]);

        $submission->refresh();

        $this->assertSame(SubmissionStatus::SelfieUploaded, $submission->status);
        $this->assertDatabaseHas('submission_assets', [
            'submission_id' => $submission->id,
            'kind'          => 'selfie',
            'disk'          => 'public',
            'path'          => "submissions/{$submission->uuid}/selfie.jpg",
        ]);
    }

    public function test_plain_javascript_selfie_endpoint_rejects_invalid_files(): void
    {
        Storage::fake('public');

        $submission = $this->createSubmission();

        $this->post(route('submission.selfie.store', $submission->tracking_token), [
            'selfie' => UploadedFile::fake()->create('selfie.pdf', 10, 'application/pdf'),
        ], [
            'Accept' => 'application/json',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('selfie');

        $this->assertDatabaseMissing('submission_assets', [
            'submission_id' => $submission->id,
            'kind'          => 'selfie',
        ]);
    }

    public function test_wizard_can_continue_after_plain_javascript_upload(): void
    {
        Storage::fake('public');

        $submission = $this->createSubmission();
        app(SubmissionService::class)->attachSelfie(
            $submission,
            UploadedFile::fake()->image('selfie.jpg', 640, 640),
        );

        Livewire::test(SubmissionWizard::class)
            ->set('step', 'selfie')
            ->set('submissionId', $submission->id)
            ->set('submissionToken', $submission->tracking_token)
            ->call('nextStep')
            ->assertSet('selfieAttached', true)
            ->assertSet('step', 'template');
    }

    private function createSubmission(): Submission
    {
        $contact = Contact::create([
            'name'  => 'Test User',
            'phone' => '+966512345678',
            'email' => 'test@example.com',
        ]);

        return Submission::create([
            'uuid'             => (string) Str::uuid(),
            'tracking_token'   => Str::random(48),
            'contact_id'       => $contact->id,
            'status'           => SubmissionStatus::FormCompleted,
            'consent_accepted' => true,
            'consented_at'     => now(),
        ]);
    }
}
