<?php

declare(strict_types=1);

namespace App\Services\Item\DTOs;

/**
 * 人気商品データDTO
 */
final class PopularItemData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $orderCount
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            orderCount: $data['order_count']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'orderCount' => $this->orderCount
        ];
    }
}
