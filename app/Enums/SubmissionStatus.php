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
    case Cancelled        = 'cancelled';

    public function label(): string
    {
        return __('status_label.' . $this->value);
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
            self::Cancelled        => 'badge badge--cancelled',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Failed, self::Cancelled]);
    }

    public function canRetry(): bool
    {
        return in_array($this, [self::Failed, self::Cancelled]);
    }

    public function canCancelQueued(): bool
    {
        return $this === self::Queued;
    }

    public function isGenerating(): bool
    {
        return in_array($this, [self::Queued, self::Processing]);
    }
}
