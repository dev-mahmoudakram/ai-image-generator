<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft            = 'draft';
    case FormCompleted    = 'form_completed';
    case SelfieUploaded   = 'selfie_uploaded';
    case TemplateSelected = 'template_selected';
    case Queued           = 'queued';
    case Processing       = 'processing';
    case Completed        = 'completed';
    case Failed           = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Draft            => 'Draft',
            self::FormCompleted    => 'Form Completed',
            self::SelfieUploaded   => 'Selfie Uploaded',
            self::TemplateSelected => 'Template Selected',
            self::Queued           => 'Queued',
            self::Processing       => 'Processing',
            self::Completed        => 'Completed',
            self::Failed           => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft,
            self::FormCompleted,
            self::SelfieUploaded,
            self::TemplateSelected => 'badge',
            self::Queued,
            self::Processing       => 'badge badge--queued',
            self::Completed        => 'badge badge--completed',
            self::Failed           => 'badge badge--failed',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Failed]);
    }

    public function canRetry(): bool
    {
        return $this === self::Failed;
    }

    public function isGenerating(): bool
    {
        return in_array($this, [self::Queued, self::Processing]);
    }
}
