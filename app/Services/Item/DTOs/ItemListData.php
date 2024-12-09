<?php

declare(strict_types=1);

namespace App\Services\Item\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 商品一覧データ
 */
class ItemListData
{
    /**
     * @param LengthAwarePaginator $items 商品データ
     */
    public function __construct(
        public readonly LengthAwarePaginator $items
    ) {
    }
}
