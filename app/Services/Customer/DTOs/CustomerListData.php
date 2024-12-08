<?php

declare(strict_types=1);

namespace App\Services\Customer\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 顧客一覧データ
 */
class CustomerListData
{
    /**
     * @param LengthAwarePaginator $customers 顧客データ
     */
    public function __construct(
        public readonly LengthAwarePaginator $customers
    ) {
    }
}
