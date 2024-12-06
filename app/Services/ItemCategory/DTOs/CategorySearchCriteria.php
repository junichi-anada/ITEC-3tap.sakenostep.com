<?php

namespace App\Services\ItemCategory\DTOs;

class CategorySearchCriteria
{
    public function __construct(
        public readonly ?int $siteId = null,
        public readonly ?int $parentId = null,
        public readonly ?string $categoryCode = null,
        public readonly ?bool $isPublished = null,
        public readonly ?array $orderBy = ['sort_order' => 'asc'],
        public readonly ?array $with = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            siteId: $data['site_id'] ?? null,
            parentId: $data['parent_id'] ?? null,
            categoryCode: $data['category_code'] ?? null,
            isPublished: $data['is_published'] ?? null,
            orderBy: $data['order_by'] ?? ['sort_order' => 'asc'],
            with: $data['with'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'site_id' => $this->siteId,
            'parent_id' => $this->parentId,
            'category_code' => $this->categoryCode,
            'is_published' => $this->isPublished,
            'order_by' => $this->orderBy,
            'with' => $this->with,
        ], fn($value) => !is_null($value));
    }

    public function getConditions(): array
    {
        $conditions = [];
        
        if ($this->siteId !== null) {
            $conditions['site_id'] = $this->siteId;
        }
        
        if ($this->parentId !== null) {
            $conditions['parent_id'] = $this->parentId;
        }
        
        if ($this->categoryCode !== null) {
            $conditions['category_code'] = $this->categoryCode;
        }
        
        if ($this->isPublished !== null) {
            $conditions['is_published'] = $this->isPublished;
        }
        
        return $conditions;
    }
}
