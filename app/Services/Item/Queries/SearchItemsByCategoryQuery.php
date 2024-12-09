<?php

namespace App\Services\Item\Queries;

use App\Services\Item\DTOs\ItemSearchCriteria;
use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
use App\Models\ItemCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SearchItemsByCategoryQuery
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository
    ) {}

    /**
     * カテゴリによる商品検索を実行
     *
     * @param ItemSearchCriteria $criteria
     * @return Collection
     * @throws ItemException
     */
    public function execute(ItemSearchCriteria $criteria): Collection
    {
        return $this->tryCatchWrapper(
            function () use ($criteria) {
                // カテゴリコードが指定されている場合、IDに変換
                if ($criteria->categoryCode) {
                    $category = ItemCategory::where('category_code', $criteria->categoryCode)
                        ->where('site_id', $criteria->siteId)
                        ->first();

                    if (!$category) {
                        Log::warning("Category not found with code", [
                            'category_code' => $criteria->categoryCode,
                            'site_id' => $criteria->siteId
                        ]);
                        return collect([]);
                    }

                    // DTOを新しいcategoryIdで作り直す
                    $criteria = new ItemSearchCriteria(
                        siteId: $criteria->siteId,
                        categoryId: $category->id,
                        isPublished: $criteria->isPublished,
                        orderBy: $criteria->orderBy,
                        perPage: $criteria->perPage,
                        with: $criteria->with
                    );
                }

                if (!$criteria->categoryId) {
                    throw ItemException::searchFailed('カテゴリが指定されていません。');
                }

                Log::info("Searching items by category", [
                    'category_id' => $criteria->categoryId
                ]);

                $result = $this->repository->search($criteria);

                if ($result->isEmpty()) {
                    Log::info("No items found for category", [
                        'category_id' => $criteria->categoryId
                    ]);
                }

                return $result;
            },
            'カテゴリによる商品検索に失敗しました',
            $criteria->toArray()
        );
    }
}
