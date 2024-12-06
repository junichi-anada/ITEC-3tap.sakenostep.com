<?php

namespace App\Services\ItemCategory\Actions;

use App\Services\ItemCategory\Exceptions\CategoryException;
use App\Services\ServiceErrorHandler;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GetCategoryBreadcrumbsAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository
    ) {}

    /**
     * カテゴリのパンくずリストを取得する
     *
     * @param int $categoryId
     * @param int $siteId
     * @return Collection
     * @throws CategoryException
     */
    public function execute(int $categoryId, int $siteId): Collection
    {
        return $this->tryCatchWrapper(
            function () use ($categoryId, $siteId) {
                Log::info("Getting breadcrumbs for category ID: $categoryId, site ID: $siteId");

                $category = $this->repository->findById($categoryId);
                if (!$category) {
                    throw CategoryException::notFound($categoryId);
                }

                $breadcrumbs = $this->repository->getBreadcrumbs($categoryId, $siteId);
                if ($breadcrumbs->isEmpty()) {
                    Log::warning("No breadcrumbs found for category ID: $categoryId");
                }

                return $breadcrumbs;
            },
            'カテゴリパンくずリストの取得に失敗しました',
            ['category_id' => $categoryId, 'site_id' => $siteId]
        );
    }
}
