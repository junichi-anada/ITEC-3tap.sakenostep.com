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

        if ($criteria->minPrice) {
            $query->where('price', '>=', $criteria->minPrice);
        }

        if ($criteria->maxPrice) {
            $query->where('price', '<=', $criteria->maxPrice);
        }

        if (isset($criteria->isPublished)) {
            $query->where('is_published', $criteria->isPublished);
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
     * 商品コードで商品を取得
     *
     * @param string $itemCode
     * @return Item|null
     */
    public function findByItemCode(string $itemCode): ?Item
    {
        return Item::where('code', $itemCode)->first();
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
}
