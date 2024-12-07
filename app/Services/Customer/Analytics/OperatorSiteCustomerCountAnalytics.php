<?php

namespace App\Services\Customer\Analytics;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class OperatorSiteCustomerCountAnalytics
{
    /**
     * サイト別の登録ユーザー数を取得
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRegisteredCustomerCountBySite()
    {
        return User::select('sites.id', 'sites.name', DB::raw('count(*) as count'))
            ->join('sites', 'users.site_id', '=', 'sites.id')
            ->groupBy('sites.id', 'sites.name')
            ->get()
            ->map(function ($item) {
                return [
                    'area' => $item->name,
                    'count' => $item->count
                ];
            });
    }
}
