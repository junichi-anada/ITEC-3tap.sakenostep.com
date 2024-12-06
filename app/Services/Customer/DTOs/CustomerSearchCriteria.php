<?php

namespace App\Services\Customer\DTOs;

class CustomerSearchCriteria
{
    public function __construct(
        public readonly ?string $keyword = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?bool $isActive = null,
        public readonly ?string $createdFrom = null,
        public readonly ?string $createdTo = null,
        public readonly ?string $sortBy = 'created_at',
        public readonly string $sortOrder = 'desc',
        public readonly int $perPage = 20
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keyword: $data['keyword'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
            createdFrom: $data['created_from'] ?? null,
            createdTo: $data['created_to'] ?? null,
            sortBy: $data['sort_by'] ?? 'created_at',
            sortOrder: $data['sort_order'] ?? 'desc',
            perPage: (int) ($data['per_page'] ?? 20)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'keyword' => $this->keyword,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->isActive,
            'created_from' => $this->createdFrom,
            'created_to' => $this->createdTo,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
            'per_page' => $this->perPage,
        ], fn($value) => $value !== null);
    }
}
