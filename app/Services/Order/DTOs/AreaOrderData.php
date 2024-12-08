<?php

declare(strict_types=1);

namespace App\Services\Order\DTOs;

/**
 * エリア別注文数のデータ転送オブジェクト
 */
final class AreaOrderData
{
    public function __construct(
        public readonly string $areaName,
        public readonly int $orderCount
    ) {}
}
