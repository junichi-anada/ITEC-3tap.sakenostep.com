<?php

namespace App\Services\Item\Queries\OperatorItemQueries;

use App\Models\Item;
use App\Services\Item\DTOs\ItemData;
use App\Services\Item\DTOs\ItemSearchCriteria;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchItemQuery
{
    use OperatorActionTrait;

    /**
     * 検索条件に基づいて商品を検索します
     *
     * @param ItemSearchCriteria $criteria
     * @param int $operatorId
     * @return LengthAwarePaginator
     */
    public function execute(ItemSearchCriteria $criteria, int $operatorId): LengthAwarePaginator
    {
        $query = Item::query()
            ->with(['category']); // カテゴリ情報を事前読み込み

        // キーワード検索
        if ($criteria->keyword) {
            $query->where(function ($q) use ($criteria) {
                $q->where('name', 'like', "%{$criteria->keyword}%")
                    ->orWhere('description', 'like', "%{$criteria->keyword}%");
            });
        }

        // カテゴリによる絞り込み
        if ($criteria->categoryId) {
            $query->where('category_id', $criteria->categoryId);
        }

        // 価格範囲による絞り込み
        if ($criteria->minPrice !== null) {
            $query->where('price', '>=', $criteria->minPrice);
        }
        if ($criteria->maxPrice !== null) {
            $query->where('price', '<=', $criteria->maxPrice);
        }

        // アクティブ状態による絞り込み
        if ($criteria->isActive !== null) {
            $query->where('is_active', $criteria->isActive);
        }

        // 在庫状態による絞り込み
        if ($criteria->hasStock !== null) {
            if ($criteria->hasStock) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '<=', 0);
            }
        }

        // 作成日時による絞り込み
        if ($criteria->createdFrom) {
            $query->where('created_at', '>=', $criteria->createdFrom);
        }
        if ($criteria->createdTo) {
            $query->where('created_at', '<=', $criteria->createdTo);
        }

        // ソート
        $query->orderBy($criteria->sortBy, $criteria->sortOrder);

        // 操作ログを記録
        $this->logOperation($operatorId, 'item.search', [
            'criteria' => $criteria->toArray()
        ]);

        // ページネーション
        return $query->paginate($criteria->perPage)
            ->through(fn($item) => ItemData::fromArray(array_merge(
                $item->toArray(),
                ['category_name' => $item->category->name ?? null]
            )));
    }

    /**
     * 検索条件に基づいて商品数を取得します
     *
     * @param ItemSearchCriteria $criteria
     * @return int
     */
    public function count(ItemSearchCriteria $criteria): int
    {
        $query = Item::query();

        if ($criteria->keyword) {
            $query->where(function ($q) use ($criteria) {
                $q->where('name', 'like', "%{$criteria->keyword}%")
                    ->orWhere('description', 'like', "%{$criteria->keyword}%");
            });
        }

        if ($criteria->categoryId) {
            $query->where('category_id', $criteria->categoryId);
        }

        if ($criteria->isActive !== null) {
            $query->where('is_active', $criteria->isActive);
        }

        return $query->count();
    }

    /**
     * カテゴリごとの商品数を取得します
     *
     * @return array
     */
    public function countByCategory(): array
    {
        return DB::table('items')
            ->join('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select('item_categories.name', DB::raw('count(*) as count'))
            ->groupBy('item_categories.id', 'item_categories.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();
    }
}
