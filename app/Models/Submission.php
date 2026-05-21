<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    protected $fillable = [
        'uuid',
        'tracking_token',
        'contact_id',
        'selected_template_id',
        'status',
        'consent_accepted',
        'consented_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'status'           => SubmissionStatus::class,
        'consent_accepted' => 'boolean',
        'consented_at'     => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'selected_template_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(SubmissionAsset::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(GenerationAttempt::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(SubmissionEvent::class);
    }

    // ── Convenience accessors ─────────────────────────────────────────────────

    public function selfie(): HasOne
    {
        return $this->hasOne(SubmissionAsset::class)
            ->where('kind', 'selfie')
            ->latestOfMany();
    }

    public function generatedImage(): HasOne
    {
        return $this->hasOne(SubmissionAsset::class)
            ->where('kind', 'generated')
            ->latestOfMany();
    }

    public function latestAttempt(): HasOne
    {
        return $this->hasOne(GenerationAttempt::class)
            ->latestOfMany('attempt_no');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function nextAttemptNo(): int
    {
        return ($this->attempts()->max('attempt_no') ?? 0) + 1;
    }

    public function logEvent(string $type, array $payload = []): SubmissionEvent
    {
        return $this->events()->create([
            'event_type' => $type,
            'payload'    => empty($payload) ? null : $payload,
        ]);
    }
}
