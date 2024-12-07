<?php

declare(strict_types=1);

namespace App\Services\Customer\DTOs;

/**
 * 顧客登録状況のデータ転送オブジェクト
 */
final class CustomerRegistrationData
{
    public function __construct(
        public readonly int $totalUsers,
        public readonly int $newUsersThisMonth,
        public readonly int $lineLinkedUsers
    ) {}
}
