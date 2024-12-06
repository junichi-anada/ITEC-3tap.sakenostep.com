<?php

namespace App\Services\Item\DTOs;

class ItemSearchCriteria
{
    public function __construct(
        public readonly ?string $keyword = null,
        public readonly ?int $siteId = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $categoryCode = null,
        public readonly ?array $orderBy = ['created_at' => 'desc'],
        public readonly int $perPage = 15,
        public readonly ?array $with = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keyword: $data['keyword'] ?? null,
            siteId: $data['site_id'] ?? null,
            categoryId: $data['category_id'] ?? null,
            categoryCode: $data['category_code'] ?? null,
            orderBy: $data['order_by'] ?? ['created_at' => 'desc'],
            perPage: $data['per_page'] ?? 15,
            with: $data['with'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'keyword' => $this->keyword,
            'site_id' => $this->siteId,
            'category_id' => $this->categoryId,
            'category_code' => $this->categoryCode,
            'order_by' => $this->orderBy,
            'per_page' => $this->perPage,
            'with' => $this->with,
        ], fn($value) => !is_null($value));
    }

    public function getConditions(): array
    {
        $conditions = [];
        
        if ($this->siteId) {
            $conditions['site_id'] = $this->siteId;
        }
        
        if ($this->categoryId) {
            $conditions['category_id'] = $this->categoryId;
        }
        
        if ($this->categoryCode) {
            $conditions['category_code'] = $this->categoryCode;
        }
        
        return $conditions;
    }
}
