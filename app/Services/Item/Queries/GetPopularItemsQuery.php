<?php

declare(strict_types=1);

namespace App\Services\Item\Queries;

use App\Models\OrderDetail;
use App\Services\Item\DTOs\PopularItemData;
use App\Services\ServiceErrorHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 人気商品ランキング取得クエリ
 */
final class GetPopularItemsQuery
{
    use ServiceErrorHandler;

    /**
     * 人気商品ランキングを取得
     *
     * @param int $limit 取得件数
     * @return Collection<PopularItemData>
     */
    public function execute(int $limit = 5): Collection
    {
        return $this->tryCatchWrapper(
            function () use ($limit) {
                return OrderDetail::select('items.id', 'items.name', DB::raw('COUNT(*) as order_count'))
                    ->join('items', 'order_details.item_id', '=', 'items.id')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->whereNull('orders.deleted_at')
                    ->groupBy('items.id', 'items.name')
                    ->orderBy('order_count', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(fn ($item) => new PopularItemData(
                        id: $item->id,
                        name: $item->name,
                        orderCount: $item->order_count
                    ));
            },
            '人気商品ランキングの取得に失敗しました',
            ['limit' => $limit]
        );
    }
}
