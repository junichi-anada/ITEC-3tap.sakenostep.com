<?php

declare(strict_types=1);

namespace App\Services\Item\Queries;

use App\Models\Item;
use App\Services\Item\DTOs\ItemListData;
use Illuminate\Database\Eloquent\Builder;

class GetItemListQuery
{
    /**
     * 商品一覧を取得する
     *
     * @param array $searchParams 検索条件
     * @param int $perPage 1ページあたりの表示件数
     * @return ItemListData
     */
    public function execute(array $searchParams = [], int $perPage = 10): ItemListData
    {
        $query = Item::query()
            ->withTrashed() // itemsテーブルの削除済みレコードも取得
            ->select('items.*')
            ->with(['category', 'unit']); // カテゴリと単位情報も取得

        // 検索条件の適用
        if (!empty($searchParams['item_code'])) {
            $query->where('items.item_code', 'like', '%' . $searchParams['item_code'] . '%');
        }

        if (!empty($searchParams['name'])) {
            $query->where('items.name', 'like', '%' . $searchParams['name'] . '%');
        }

        if (!empty($searchParams['category_id'])) {
            $query->where('items.category_id', '=', $searchParams['category_id']);
        }

        if (!empty($searchParams['published_at_from'])) {
            $query->where('items.published_at', '>=', $searchParams['published_at_from']);
        }

        if (!empty($searchParams['published_at_to'])) {
            $query->where('items.published_at', '<=', $searchParams['published_at_to'] . ' 23:59:59');
        }

        if (!empty($searchParams['from_source'])) {
            $query->where('items.from_source', '=', $searchParams['from_source']);
        }

        if (isset($searchParams['is_recommended'])) {
            $query->where('items.is_recommended', '=', $searchParams['is_recommended']);
        }

        $items = $query->paginate($perPage);

        return new ItemListData($items);
    }
}
