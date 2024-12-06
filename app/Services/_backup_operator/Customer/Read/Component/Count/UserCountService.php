<?php

namespace App\Services\Operator\Customer\Read\Component\Count;

use App\Models\User;

final class UserCountService
{
    /**
     * 全ユーザー数を取得
     *
     * @return int
     */
    public function getUserCount(): int
    {
        return User::count();
    }
}
