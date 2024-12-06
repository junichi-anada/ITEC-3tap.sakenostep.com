<?php

namespace App\Services\Item\Queries;

use App\Services\Item\DTOs\ItemSearchCriteria;
use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
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
                if (!$criteria->categoryId && !$criteria->categoryCode) {
                    throw ItemException::searchFailed('カテゴリが指定されていません。');
                }

                Log::info("Searching items by category", [
                    'category_id' => $criteria->categoryId,
                    'category_code' => $criteria->categoryCode
                ]);

                $result = $this->repository->search($criteria);

                if ($result->isEmpty()) {
                    Log::info("No items found for category", [
                        'category_id' => $criteria->categoryId,
                        'category_code' => $criteria->categoryCode
                    ]);
                }

                return $result;
            },
            'カテゴリによる商品検索に失敗しました',
            $criteria->toArray()
        );
    }
}
