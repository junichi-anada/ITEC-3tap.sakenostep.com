<?php

declare(strict_types=1);

namespace App\Services\ItemCategory;

use App\Models\ItemCategory;
use App\Services\ItemCategory\Actions\CreateCategoryAction;
use App\Services\ItemCategory\Actions\UpdateCategoryAction;
use App\Services\ItemCategory\Actions\DeleteCategoryAction;
use App\Services\ItemCategory\Actions\GetCategoryBreadcrumbsAction;
use App\Services\ItemCategory\Queries\GetPublishedCategoriesQuery;
use App\Services\ItemCategory\Queries\GetSubCategoriesQuery;
use App\Services\ItemCategory\DTOs\CategoryData;
use App\Services\ItemCategory\DTOs\CategorySearchCriteria;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use App\Services\ServiceErrorHandler;
use Illuminate\Database\Eloquent\Collection;

/**
 * 商品カテゴリサービスクラス
 *
 * このクラスは商品カテゴリに関する操作のファサードとして機能し、
 * 具体的な処理を各ActionクラスとQueryクラスに委譲します。
 */
final class ItemCategoryService
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository,
        private CreateCategoryAction $createCategoryAction,
        private UpdateCategoryAction $updateCategoryAction,
        private DeleteCategoryAction $deleteCategoryAction,
        private GetCategoryBreadcrumbsAction $getCategoryBreadcrumbsAction,
        private GetPublishedCategoriesQuery $getPublishedCategoriesQuery,
        private GetSubCategoriesQuery $getSubCategoriesQuery
    ) {}

    /**
     * カテゴリを作成
     *
     * @param array $data
     * @return ItemCategory
     */
    public function create(array $data): ItemCategory
    {
        return $this->createCategoryAction->execute(CategoryData::fromArray($data));
    }

    /**
     * カテゴリを更新
     *
     * @param int $id
     * @param array $data
     * @return ItemCategory
     */
    public function update(int $id, array $data): ItemCategory
    {
        return $this->updateCategoryAction->execute($id, CategoryData::fromArray($data));
    }

    /**
     * カテゴリを削除
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->deleteCategoryAction->execute($id);
    }

    /**
     * IDでカテゴリを取得
     *
     * @param int $id
     * @return ItemCategory|null
     */
    public function findById(int $id): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->repository->findById($id),
            'カテゴリの取得に失敗しました',
            ['id' => $id]
        );
    }

    /**
     * サイトの全カテゴリ一覧を取得
     *
     * @param int $siteId
     * @return Collection
     */
    public function getAllCategories(int $siteId): Collection
    {
        $criteria = new CategorySearchCriteria(siteId: $siteId);
        return $this->tryCatchWrapper(
            fn() => $this->repository->findBy($criteria->getConditions()),
            'カテゴリ一覧の取得に失敗しました',
            ['site_id' => $siteId]
        );
    }

    /**
     * サイトIDに基づいてカテゴリを取得
     *
     * @param int $siteId
     * @return Collection
     */
    public function getBySiteId(int $siteId): Collection
    {
        return $this->getAllCategories($siteId);
    }

    /**
     * 公開状態のカテゴリのみを取得
     *
     * @param int $siteId
     * @return Collection
     */
    public function getPublishedCategories(int $siteId): Collection
    {
        $criteria = new CategorySearchCriteria(siteId: $siteId);
        return $this->getPublishedCategoriesQuery->execute($criteria);
    }

    /**
     * カテゴリのパンくずリストを取得
     *
     * @param int $categoryId
     * @param int $siteId
     * @return Collection
     */
    public function getCategoryBreadcrumbs(int $categoryId, int $siteId): Collection
    {
        return $this->getCategoryBreadcrumbsAction->execute($categoryId, $siteId);
    }

    /**
     * サイトIDとカテゴリコードでカテゴリを検索
     *
     * @param int $siteId
     * @param string $categoryCode
     * @return ItemCategory|null
     */
    public function getByCategoryCode(int $siteId, string $categoryCode): ?ItemCategory
    {
        return $this->tryCatchWrapper(
            fn() => $this->repository->findByCategoryCode($categoryCode, $siteId),
            'カテゴリコードによる検索に失敗しました',
            ['site_id' => $siteId, 'category_code' => $categoryCode]
        );
    }

    /**
     * 親カテゴリに基づくサブカテゴリを取得
     *
     * @param int $parentId
     * @param int $siteId
     * @return Collection
     */
    public function getSubCategories(int $parentId, int $siteId): Collection
    {
        $criteria = new CategorySearchCriteria(siteId: $siteId);
        return $this->getSubCategoriesQuery->execute($parentId, $criteria);
    }
}
