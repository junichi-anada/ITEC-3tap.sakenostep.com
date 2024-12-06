<?php

namespace App\Services\Site\DTOs;

class SiteData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $siteCode = null,
        public readonly ?int $companyId = null,
        public readonly ?string $domain = null,
        public readonly ?bool $isActive = true,
        public readonly ?array $settings = [],
        public readonly ?array $attributes = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            siteCode: $data['site_code'] ?? null,
            companyId: $data['company_id'] ?? null,
            domain: $data['domain'] ?? null,
            isActive: $data['is_active'] ?? true,
            settings: $data['settings'] ?? [],
            attributes: $data['attributes'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'site_code' => $this->siteCode,
            'company_id' => $this->companyId,
            'domain' => $this->domain,
            'is_active' => $this->isActive,
            'settings' => $this->settings,
            'attributes' => $this->attributes,
        ], fn($value) => !is_null($value));
    }
}
