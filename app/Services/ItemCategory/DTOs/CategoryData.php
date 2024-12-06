<?php

namespace App\Services\ItemCategory\DTOs;

class CategoryData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $categoryCode = null,
        public readonly ?int $siteId = null,
        public readonly ?int $parentId = null,
        public readonly ?string $description = null,
        public readonly ?bool $isPublished = true,
        public readonly ?int $sortOrder = 0,
        public readonly ?array $attributes = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            categoryCode: $data['category_code'] ?? null,
            siteId: $data['site_id'] ?? null,
            parentId: $data['parent_id'] ?? null,
            description: $data['description'] ?? null,
            isPublished: $data['is_published'] ?? true,
            sortOrder: $data['sort_order'] ?? 0,
            attributes: $data['attributes'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'category_code' => $this->categoryCode,
            'site_id' => $this->siteId,
            'parent_id' => $this->parentId,
            'description' => $this->description,
            'is_published' => $this->isPublished,
            'sort_order' => $this->sortOrder,
            'attributes' => $this->attributes,
        ], fn($value) => !is_null($value));
    }
}
