<?php

namespace App\Services\ItemCategory\Queries;

use App\Services\ItemCategory\DTOs\CategorySearchCriteria;
use App\Services\ItemCategory\Exceptions\CategoryException;
use App\Services\ServiceErrorHandler;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GetSubCategoriesQuery
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository
    ) {}

    /**
     * サブカテゴリを取得する
     *
     * @param int $parentId
     * @param CategorySearchCriteria $criteria
     * @return Collection
     * @throws CategoryException
     */
    public function execute(int $parentId, CategorySearchCriteria $criteria): Collection
    {
        return $this->tryCatchWrapper(
            function () use ($parentId, $criteria) {
                Log::info("Getting sub-categories for parent ID: $parentId, site ID: {$criteria->siteId}");

                // 親カテゴリの存在確認
                $parentCategory = $this->repository->findById($parentId);
                if (!$parentCategory) {
                    throw CategoryException::notFound($parentId);
                }

                $conditions = array_merge(
                    $criteria->getConditions(),
                    ['parent_id' => $parentId]
                );

                $categories = $this->repository->findBy(
                    conditions: $conditions,
                    orderBy: $criteria->orderBy,
                    with: $criteria->with
                );

                if ($categories->isEmpty()) {
                    Log::warning("No sub-categories found for parent ID: $parentId");
                }

                return $categories;
            },
            'サブカテゴリの取得に失敗しました',
            ['parent_id' => $parentId] + $criteria->toArray()
        );
    }
}
