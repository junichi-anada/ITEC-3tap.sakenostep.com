<?php

declare(strict_types=1);

namespace App\Services\Order\Analytics;

use App\Models\Item;
use App\Models\OrderDetail;
use App\Services\Order\DTOs\PopularItemData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 人気商品分析サービス
 */
final class PopularItemAnalytics
{
    /**
     * 人気商品のランキングを取得
     *
     * @param int $limit 取得する商品数
     * @return Collection<PopularItemData>
     */
    public function getPopularItems(int $limit = 5): Collection
    {
        $popularItems = OrderDetail::query()
            ->select('item_id', DB::raw('sum(volume) as total_volume'))
            ->groupBy('item_id')
            ->orderByDesc('total_volume')
            ->limit($limit)
            ->get();

        $itemIds = $popularItems->pluck('item_id');
        $items = Item::whereIn('id', $itemIds)->get();

        return $popularItems->map(function ($popularItem) use ($items) {
            $item = $items->firstWhere('id', $popularItem->item_id);
            
            return new PopularItemData(
                itemId: $item->id,
                name: $item->name,
                totalVolume: $popularItem->total_volume,
                price: $item->price,
                description: $item->description
            );
        });
    }

    /**
     * 期間を指定して人気商品を取得
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return Collection<PopularItemData>
     */
    public function getPopularItemsByPeriod(
        string $startDate,
        string $endDate,
        int $limit = 5
    ): Collection {
        $popularItems = OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('item_id', DB::raw('sum(volume) as total_volume'))
            ->groupBy('item_id')
            ->orderByDesc('total_volume')
            ->limit($limit)
            ->get();

        $itemIds = $popularItems->pluck('item_id');
        $items = Item::whereIn('id', $itemIds)->get();

        return $popularItems->map(function ($popularItem) use ($items) {
            $item = $items->firstWhere('id', $popularItem->item_id);
            
            return new PopularItemData(
                itemId: $item->id,
                name: $item->name,
                totalVolume: $popularItem->total_volume,
                price: $item->price,
                description: $item->description
            );
        });
    }
}
