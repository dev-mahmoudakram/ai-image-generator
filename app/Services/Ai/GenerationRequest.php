<?php

namespace App\Services\Ai;

readonly class GenerationRequest
{
    public function __construct(
        public string $selfiePath,
        public string $selfieDisk,
        public string $templatePath,
        public string $templateDisk,
        public string $prompt,
    ) {}
}
