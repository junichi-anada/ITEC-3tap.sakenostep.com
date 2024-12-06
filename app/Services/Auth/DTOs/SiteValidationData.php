<?php

namespace App\Services\Auth\DTOs;

class SiteValidationData
{
    public function __construct(
        public readonly string $siteCode
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            siteCode: $data['site_code']
        );
    }
}
