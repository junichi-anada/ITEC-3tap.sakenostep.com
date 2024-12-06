<?php

declare(strict_types=1);

namespace App\Services\Order\Analytics;

use App\Models\Order;
use App\Services\Order\DTOs\AreaOrderData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 地域ごとの注文分析サービス
 */
final class AreaOrderAnalytics
{
    /**
     * 地域ごとの注文数を取得
     *
     * @return Collection<string, int> [ key: 地域, value: 注文数 ]
     */
    public function getOrderCountByArea(): Collection
    {
        $siteId = Auth::user()->site_id;

        $orders = Order::query()
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('users.site_id', $siteId)
            ->select('orders.*', 'users.name as user_name', 'users.address')
            ->get();

        return $orders->groupBy(function ($order) {
            return $this->extractArea($order->address);
        })->map(function ($areaOrders) {
            return $areaOrders->count();
        });
    }

    /**
     * 地域ごとの注文データを取得
     *
     * @return Collection<AreaOrderData>
     */
    public function getAreaOrderDetails(): Collection
    {
        $orderCounts = $this->getOrderCountByArea();

        return $orderCounts->map(function ($count, $area) {
            return new AreaOrderData(
                area: $area,
                orderCount: $count
            );
        });
    }

    /**
     * 住所から地域を抽出
     */
    private function extractArea(string $address): string
    {
        if (preg_match('/^(.+?(都|道|府|県).+?(市|区|町|村))/u', $address, $matches) && isset($matches[1])) {
            return $matches[1];
        }
        return '不明';
    }
}
