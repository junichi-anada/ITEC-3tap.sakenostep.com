<?php

namespace App\Services\ItemCategory\DTOs;

class CategorySearchCriteria
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $categoryCode = null,
        public readonly ?int $siteId = null,
        public readonly ?bool $isPublished = null,
        public readonly ?array $orderBy = ['priority' => 'asc'],
        public readonly ?array $with = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            categoryCode: $data['category_code'] ?? null,
            siteId: $data['site_id'] ?? null,
            isPublished: $data['is_published'] ?? null,
            orderBy: $data['order_by'] ?? ['priority' => 'asc'],
            with: $data['with'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'category_code' => $this->categoryCode,
            'site_id' => $this->siteId,
            'is_published' => $this->isPublished,
            'order_by' => $this->orderBy,
            'with' => $this->with,
        ], fn($value) => !is_null($value));
    }

    public function getConditions(): array
    {
        return array_filter([
            'name' => $this->name,
            'category_code' => $this->categoryCode,
            'site_id' => $this->siteId,
        ], fn($value) => !is_null($value));
    }
}
