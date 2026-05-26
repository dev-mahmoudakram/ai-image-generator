<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Submission;
use App\Models\Template;
use App\Rules\ValidPhoneNumber;
use App\Services\PhoneNumberService;
use App\Services\SubmissionService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.public')]
class SubmissionWizard extends Component
{
    use WithFileUploads;

    public string $step = 'form';

    public string $name    = '';
    public string $phone   = '';
    public string $email   = '';
    public bool   $consent = false;

    public $selfie = null;

    public ?int    $submissionId    = null;
    public ?string $submissionToken = null;
    public bool    $selfieAttached  = false;

    public function submitForm(): void
    {
        $this->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', new ValidPhoneNumber()],
            'email'   => ['nullable', 'email', 'max:255'],
            'consent' => ['accepted'],
        ]);

        $e164    = app(PhoneNumberService::class)->toE164($this->phone);
        $contact = Contact::where('phone', $e164)->first();

        if ($contact && $contact->submissions()->exists()) {
            $this->addError('phone', __('wizard.phone_already_submitted'));
            return;
        }

        $submission = app(SubmissionService::class)->start(
            ['name' => $this->name, 'phone' => $this->phone, 'email' => $this->email],
            request(),
        );

        $this->submissionId    = $submission->id;
        $this->submissionToken = $submission->tracking_token;
        $this->resetErrorBag();
        $this->step = 'selfie';
    }

    public function updatedSelfie(): void
    {
        $this->validateOnly('selfie', [
            'selfie' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $submission = Submission::findOrFail($this->submissionId);
        app(SubmissionService::class)->attachSelfie($submission, $this->selfie);

        $this->selfie         = null;
        $this->selfieAttached = true;
        $this->resetErrorBag();
    }

    public function nextStep(): void
    {
        $submission = $this->submissionId
            ? Submission::with('selfie')->find($this->submissionId)
            : null;

        abort_unless($submission && ($this->selfieAttached || $submission->selfie), 422);

        $this->selfieAttached = true;
        $this->step = 'template';
    }

    public function chooseTemplate(int $templateId): void
    {
        $submission = Submission::findOrFail($this->submissionId);
        $template   = Template::findOrFail($templateId);

        $service = app(SubmissionService::class);
        $service->selectTemplate($submission, $template);
        $service->queueGeneration($submission);

        $this->redirect(route('submission.track', $this->submissionToken));
    }

    public function render(): View
    {
        return view('livewire.submission-wizard', [
            'templates' => $this->step === 'template'
                ? Template::active()->ordered()->get()
                : collect(),
        ]);
    }
}
