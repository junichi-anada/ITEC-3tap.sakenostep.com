<?php

declare(strict_types=1);

namespace App\Services\Order\DTOs;

/**
 * 地域ごとの注文データ転送オブジェクト
 */
final class AreaOrderData
{
    public function __construct(
        public readonly string $area,
        public readonly int $orderCount
    ) {}

    /**
     * 配列に変換
     */
    public function toArray(): array
    {
        return [
            'area' => $this->area,
            'order_count' => $this->orderCount,
        ];
    }
}
