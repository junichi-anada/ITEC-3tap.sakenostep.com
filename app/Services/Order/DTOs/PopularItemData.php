<?php

declare(strict_types=1);

namespace App\Services\Order\DTOs;

/**
 * 人気商品データ転送オブジェクト
 */
final class PopularItemData
{
    public function __construct(
        public readonly int $itemId,
        public readonly string $name,
        public readonly int $totalVolume,
        public readonly int $price,
        public readonly ?string $description = null
    ) {}

    /**
     * 配列に変換
     */
    public function toArray(): array
    {
        return [
            'item_id' => $this->itemId,
            'name' => $this->name,
            'total_volume' => $this->totalVolume,
            'price' => $this->price,
            'description' => $this->description,
        ];
    }

    /**
     * 売上金額を計算
     */
    public function getTotalAmount(): int
    {
        return $this->totalVolume * $this->price;
    }
}
