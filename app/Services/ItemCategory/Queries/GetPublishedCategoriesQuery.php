<?php

namespace App\Services\ItemCategory\Queries;

use App\Services\ItemCategory\DTOs\CategorySearchCriteria;
use App\Services\ItemCategory\Exceptions\CategoryException;
use App\Services\ServiceErrorHandler;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GetPublishedCategoriesQuery
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository
    ) {}

    /**
     * 公開状態のカテゴリを取得する
     *
     * @param CategorySearchCriteria $criteria
     * @return Collection
     * @throws CategoryException
     */
    public function execute(CategorySearchCriteria $criteria): Collection
    {
        return $this->tryCatchWrapper(
            function () use ($criteria) {
                Log::info("Getting published categories for site ID: {$criteria->siteId}");

                $conditions = array_merge(
                    $criteria->getConditions(),
                    ['is_published' => true]
                );

                $categories = $this->repository->findBy(
                    conditions: $conditions,
                    orderBy: $criteria->orderBy,
                    with: $criteria->with
                );

                if ($categories->isEmpty()) {
                    Log::warning("No published categories found for site ID: {$criteria->siteId}");
                }

                return $categories;
            },
            '公開カテゴリ一覧の取得に失敗しました',
            $criteria->toArray()
        );
    }
}
