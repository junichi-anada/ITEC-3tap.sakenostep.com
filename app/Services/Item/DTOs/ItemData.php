<?php

namespace App\Services\Item\DTOs;

class ItemData
{
    public function __construct(
        public readonly ?string $itemCode = null,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $siteId = null,
        public readonly ?float $price = null,
        public readonly ?int $stock = null,
        public readonly ?array $attributes = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            itemCode: $data['item_code'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            categoryId: $data['category_id'] ?? null,
            siteId: $data['site_id'] ?? null,
            price: $data['price'] ?? null,
            stock: $data['stock'] ?? null,
            attributes: $data['attributes'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'item_code' => $this->itemCode,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'site_id' => $this->siteId,
            'price' => $this->price,
            'stock' => $this->stock,
            'attributes' => $this->attributes,
        ], fn($value) => !is_null($value));
    }
}
