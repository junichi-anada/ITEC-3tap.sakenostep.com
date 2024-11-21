<?php

namespace App\Services\Operator\Customer\Read\Component\Count;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class NewUserCountService
{
    /**
     * 本日の新規ユーザー数を取得
     *
     * @return int
     */
    public function getNewUserCount(): int
    {
        return User::whereDate('created_at', today())->count();
    }
}
