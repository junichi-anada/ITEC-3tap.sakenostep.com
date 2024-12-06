<?php

namespace App\Services\Auth\DTOs;

class UserAuthenticationData
{
    public function __construct(
        public readonly string $loginCode,
        public readonly int $siteId,
        public readonly string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            loginCode: $data['login_code'],
            siteId: $data['site_id'],
            password: $data['password']
        );
    }
}
