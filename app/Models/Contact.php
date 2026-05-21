<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
