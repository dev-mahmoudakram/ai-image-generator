<?php

namespace App\Services;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    private PhoneNumberUtil $util;
    private string $defaultRegion;

    public function __construct()
    {
        $this->util          = PhoneNumberUtil::getInstance();
        $this->defaultRegion = config('contact.phone_default_region', 'SA');
    }

    public function isValid(string $input): bool
    {
        try {
            $parsed = $this->util->parse($input, $this->defaultRegion);
            return $this->util->isValidNumber($parsed);
        } catch (NumberParseException) {
            return false;
        }
    }

    public function toE164(string $input): string
    {
        $parsed = $this->util->parse($input, $this->defaultRegion);
        return $this->util->format($parsed, PhoneNumberFormat::E164);
    }

    public function toInternational(string $e164): string
    {
        try {
            $parsed = $this->util->parse($e164, null);
            return $this->util->format($parsed, PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException) {
            return $e164;
        }
    }
}
