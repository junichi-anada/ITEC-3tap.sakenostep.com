<?php

declare(strict_types=1);

namespace App\Services\Order\Analytics;

use App\Models\Order;
use App\Services\Order\DTOs\AreaOrderData;
use App\Services\ServiceErrorHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * エリア別注文数の分析クラス
 */
final class AreaOrderAnalytics
{
    use ServiceErrorHandler;

    /**
     * エリア別の注文数を取得
     * 
     * 地域は「都道府県＋市区町村」でグループ化される
     * 例：東京都渋谷区、大阪府大阪市など
     *
     * @return array<string, int>
     */
    public function getOrdersByArea(): array
    {
        return $this->tryCatchWrapper(
            callback: function () {
                $orders = Order::select('site_id', DB::raw('count(*) as count'))
                    ->join('sites', 'orders.site_id', '=', 'sites.id')
                    ->whereDate('orders.created_at', now()->toDateString())
                    ->groupBy('site_id')
                    ->with('site:id,name')
                    ->get()
                    ->map(fn ($order) => new AreaOrderData(
                        areaName: $order->site->name ?? '未設定',
                        orderCount: $order->count
                    ));

                // 地域名でソートして返却
                return $orders->sortBy('areaName')
                    ->mapWithKeys(fn (AreaOrderData $data) => [
                        $data->areaName => $data->orderCount
                    ])->all();
            },
            errorMessage: 'エリア別注文数の取得に失敗しました'
        );
    }

    /**
     * 月間の注文総数を取得
     *
     * @return int
     */
    public function getMonthlyOrderCount(): int
    {
        return $this->tryCatchWrapper(
            callback: function () {
                $now = Carbon::now();
                return Order::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->count();
            },
            errorMessage: '月間注文数の取得に失敗しました'
        );
    }
}
