<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationAttempt extends Model
{
    protected $fillable = [
        'submission_id',
        'attempt_no',
        'status',
        'provider',
        'model',
        'prompt',
        'selfie_asset_id',
        'generated_asset_id',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'attempt_no'   => 'integer',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function selfieAsset(): BelongsTo
    {
        return $this->belongsTo(SubmissionAsset::class, 'selfie_asset_id');
    }

    public function generatedAsset(): BelongsTo
    {
        return $this->belongsTo(SubmissionAsset::class, 'generated_asset_id');
    }
}
