<?php

declare(strict_types=1);

namespace App\Services\Item;

use App\Models\Item;
use App\Services\Item\Actions\CreateItemAction;
use App\Services\Item\Actions\UpdateItemAction;
use App\Services\Item\Actions\DeleteItemAction;
use App\Services\Item\Actions\GetRecommendedItemsAction;
use App\Services\Item\Queries\SearchItemsByKeywordQuery;
use App\Services\Item\Queries\SearchItemsByCategoryQuery;
use App\Services\Item\DTOs\ItemData;
use App\Services\Item\DTOs\ItemSearchCriteria;
use App\Repositories\Item\ItemRepository;
use App\Services\ServiceErrorHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 商品サービスクラス
 *
 * このクラスは商品に関する操作のファサードとして機能し、
 * 具体的な処理を各ActionクラスとQueryクラスに委譲します。
 */
final class ItemService
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository,
        private CreateItemAction $createItemAction,
        private UpdateItemAction $updateItemAction,
        private DeleteItemAction $deleteItemAction,
        private GetRecommendedItemsAction $getRecommendedItemsAction,
        private SearchItemsByKeywordQuery $searchItemsByKeywordQuery,
        private SearchItemsByCategoryQuery $searchItemsByCategoryQuery
    ) {}

    /**
     * 新しい商品を作成する
     *
     * @param array $data
     * @return Item
     */
    public function create(array $data): Item
    {
        return $this->createItemAction->execute(ItemData::fromArray($data));
    }

    /**
     * 商品を更新する
     *
     * @param string $itemCode
     * @param array $data
     * @return bool
     */
    public function update(string $itemCode, array $data): bool
    {
        return $this->updateItemAction->execute($itemCode, ItemData::fromArray($data));
    }

    /**
     * 商品を削除する
     *
     * @param string $itemCode
     * @return bool
     */
    public function delete(string $itemCode): bool
    {
        return $this->deleteItemAction->execute($itemCode);
    }

    /**
     * IDで商品を取得する
     *
     * @param int $itemId
     * @return Item|null
     */
    public function getById(int $itemId): ?Item
    {
        return $this->tryCatchWrapper(
            fn () => $this->repository->findById($itemId),
            '商品情報の取得に失敗しました'
        );
    }

    /**
     * 商品コードで商品を取得する
     *
     * @param string $itemCode
     * @param int $siteId
     * @return Item|null
     */
    public function getByCode(string $itemCode, int $siteId): ?Item
    {
        return $this->tryCatchWrapper(
            fn () => $this->repository->findByCode($itemCode, $siteId),
            '商品コードによる商品情報の取得に失敗しました'
        );
    }

    /**
     * キーワードによる商品検索を実行
     *
     * @param string $keyword
     * @param int $siteId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchByKeyword(string $keyword, int $siteId, int $perPage = 15): LengthAwarePaginator
    {
        $criteria = new ItemSearchCriteria(
            keyword: $keyword,
            siteId: $siteId,
            perPage: $perPage
        );
        return $this->searchItemsByKeywordQuery->execute($criteria);
    }

    /**
     * カテゴリによる商品検索を実行
     *
     * @param array $criteria
     * @return Collection
     */
    public function searchByCategory(array $criteria): Collection
    {
        return $this->searchItemsByCategoryQuery->execute(ItemSearchCriteria::fromArray($criteria));
    }

    /**
     * おすすめ商品を取得する
     *
     * @param int $siteId
     * @param int $limit
     * @return Collection
     */
    public function getRecommendedItems(int $siteId, int $limit = 10): Collection
    {
        return $this->getRecommendedItemsAction->execute($siteId, $limit);
    }

    /**
     * 全ての商品を取得する
     *
     * @return Collection|null
     */
    public function getAll(): ?Collection
    {
        return $this->tryCatchWrapper(
            fn () => $this->repository->all(),
            '全商品情報の取得に失敗しました'
        );
    }
}
