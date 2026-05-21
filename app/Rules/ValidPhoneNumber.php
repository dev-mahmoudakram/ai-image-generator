<?php

namespace App\Rules;

use App\Services\PhoneNumberService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(PhoneNumberService::class)->isValid((string) $value)) {
            $fail('The :attribute must be a valid phone number.');
        }
    }
}
