<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Jobs\GenerateSubmissionImageJob;
use App\Models\Contact;
use App\Models\Submission;
use App\Models\SubmissionAsset;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class SubmissionService
{
    public function __construct(
        private PhoneNumberService $phone,
        private ImageStorageService $storage,
    ) {}

    // ── Step 1: create submission from form ───────────────────────────────────

    public function start(array $data, Request $request): Submission
    {
        $e164    = $this->phone->toE164($data['phone']);
        $contact = Contact::firstOrCreate(
            ['phone' => $e164],
            [
                'name'  => $data['name'],
                'email' => $data['email'] ?? null,
            ]
        );

        $submission = Submission::create([
            'uuid'             => Str::uuid(),
            'tracking_token'   => Str::random(48),
            'contact_id'       => $contact->id,
            'status'           => SubmissionStatus::FormCompleted,
            'consent_accepted' => true,
            'consented_at'     => now(),
            'ip_address'       => $request->ip(),
            'user_agent'       => substr($request->userAgent() ?? '', 0, 512),
        ]);

        $submission->logEvent('form_completed', [
            'contact_id' => $contact->id,
        ]);

        return $submission;
    }

    // ── Step 2: attach selfie ─────────────────────────────────────────────────

    public function attachSelfie(Submission $submission, UploadedFile $file): SubmissionAsset
    {
        $asset = $this->storage->storeSelfie($submission, $file);

        $submission->update(['status' => SubmissionStatus::SelfieUploaded]);
        $submission->logEvent('selfie_uploaded', ['asset_id' => $asset->id]);

        return $asset;
    }

    // ── Step 3: select template ───────────────────────────────────────────────

    public function selectTemplate(Submission $submission, Template $template): void
    {
        $submission->update([
            'selected_template_id' => $template->id,
            'status'               => SubmissionStatus::TemplateSelected,
        ]);

        $submission->logEvent('template_selected', ['template_id' => $template->id]);
    }

    // ── Step 4: queue generation ──────────────────────────────────────────────

    public function queueGeneration(Submission $submission): void
    {
        $submission->update(['status' => SubmissionStatus::Queued]);
        $submission->logEvent('queued');

        GenerateSubmissionImageJob::dispatch($submission->id);
    }

    // ── Job callbacks ─────────────────────────────────────────────────────────

    public function markCompleted(Submission $submission, SubmissionAsset $generatedAsset): void
    {
        $submission->update(['status' => SubmissionStatus::Completed]);
        $submission->logEvent('completed', ['asset_id' => $generatedAsset->id]);
    }

    public function markFailed(Submission $submission, string $error): void
    {
        $submission->update(['status' => SubmissionStatus::Failed]);
        $submission->logEvent('failed', ['error' => $error]);
    }

    // ── Admin: retry failed ───────────────────────────────────────────────────

    public function retry(Submission $submission): void
    {
        abort_unless($submission->status->canRetry(), 422, 'Submission cannot be retried.');

        $submission->update(['status' => SubmissionStatus::Queued]);
        $submission->logEvent('retry_requested');

        GenerateSubmissionImageJob::dispatch($submission->id);
    }

    // ── Token lookup ──────────────────────────────────────────────────────────

    public function findByToken(string $token): Submission
    {
        return Submission::where('tracking_token', $token)->firstOrFail();
    }
}
