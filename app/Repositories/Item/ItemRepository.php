<?php

namespace App\Repositories\Item;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Item\DTOs\ItemSearchCriteria;

class ItemRepository
{
    /**
     * 検索条件に基づいて商品を検索
     *
     * @param ItemSearchCriteria $criteria
     * @return Collection
     */
    public function search(ItemSearchCriteria $criteria): Collection
    {
        $query = Item::query();

        if ($criteria->keyword) {
            $query->where(function ($q) use ($criteria) {
                $q->where('name', 'LIKE', "%{$criteria->keyword}%")
                  ->orWhere('description', 'LIKE', "%{$criteria->keyword}%");
            });
        }

        if ($criteria->categoryId) {
            $query->where('category_id', $criteria->categoryId);
        }

        if ($criteria->siteId) {
            $query->where('site_id', $criteria->siteId);
        }

        if ($criteria->minPrice !== null) {
            $query->where('price', '>=', $criteria->minPrice);
        }

        if ($criteria->maxPrice !== null) {
            $query->where('price', '<=', $criteria->maxPrice);
        }

        if ($criteria->isPublished === true) {
            $query->whereNotNull('published_at');
        } elseif ($criteria->isPublished === false) {
            $query->whereNull('published_at');
        }

        if (!empty($criteria->orderBy)) {
            foreach ($criteria->orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        if (!empty($criteria->with)) {
            $query->with($criteria->with);
        }

        return $query->get();
    }

    /**
     * IDで商品を取得
     *
     * @param int $id
     * @return Item|null
     */
    public function find(int $id): ?Item
    {
        return Item::find($id);
    }

    /**
     * 商品コードとサイトIDで商品を取得
     *
     * @param string $itemCode
     * @param int $siteId
     * @return Item|null
     */
    public function findByCode(string $itemCode, int $siteId): ?Item
    {
        return Item::where('item_code', $itemCode)
            ->where('site_id', $siteId)
            ->first();
    }

    /**
     * おすすめ商品を取得
     *
     * @param int $siteId
     * @param int $limit
     * @return Collection
     */
    public function findRecommendedItems(int $siteId, int $limit = 10): Collection
    {
        return Item::where('site_id', $siteId)
            ->whereNotNull('published_at')
            ->where('is_recommended', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 商品を作成
     *
     * @param array $data
     * @return Item
     */
    public function create(array $data): Item
    {
        return Item::create($data);
    }

    /**
     * 商品を更新
     *
     * @param Item $item
     * @param array $data
     * @return bool
     */
    public function update(Item $item, array $data): bool
    {
        return $item->update($data);
    }

    /**
     * 商品を削除
     *
     * @param Item $item
     * @return bool
     */
    public function delete(Item $item): bool
    {
        return $item->delete();
    }

    /**
     * 全ての商品を取得
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Item::all();
    }
}
