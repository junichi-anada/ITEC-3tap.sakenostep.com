<?php

namespace App\Services\Customer\Analytics;

use App\Models\User;
use App\Models\LineUser;
use App\Services\Customer\DTOs\CustomerRegistrationData;
use App\Services\ServiceErrorHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperatorCustomerCountAnalytics
{
    use ServiceErrorHandler;

    /**
     * 顧客登録状況の概要を取得
     *
     * @return CustomerRegistrationData
     */
    public function getCustomerRegistrationSummary(): CustomerRegistrationData
    {
        return $this->tryCatchWrapper(
            callback: function () {
                $now = Carbon::now();

                // 全ユーザー数
                $totalUsers = User::whereNull('deleted_at')->count();

                // 当月の新規登録数
                $newUsersThisMonth = User::whereNull('deleted_at')
                    ->whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->count();

                // LINE連携済みユーザー数
                $lineLinkedUsers = LineUser::whereNull('deleted_at')->count();

                return new CustomerRegistrationData(
                    totalUsers: $totalUsers,
                    newUsersThisMonth: $newUsersThisMonth,
                    lineLinkedUsers: $lineLinkedUsers
                );
            },
            errorMessage: '顧客登録状況の取得に失敗しました'
        );
    }
}
