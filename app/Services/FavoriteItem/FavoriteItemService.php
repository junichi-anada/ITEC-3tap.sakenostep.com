<?php

declare(strict_types=1);

namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Services\FavoriteItem\Actions\AddFavoriteItemAction;
use App\Services\FavoriteItem\Actions\RemoveFavoriteItemAction;
use App\Services\FavoriteItem\Actions\RestoreFavoriteItemAction;
use App\Services\FavoriteItem\DTOs\FavoriteItemData;
use App\Services\FavoriteItem\DTOs\FavoriteItemSearchCriteria;
use App\Services\FavoriteItem\Traits\FavoriteItemConditionTrait;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use App\Services\ServiceErrorHandler;
use Illuminate\Database\Eloquent\Collection;

/**
 * お気に入り商品サービスクラス
 *
 * このクラスはお気に入り商品に関する操作のファサードとして機能し、
 * 具体的な処理を各Actionクラスに委譲します。
 */
final class FavoriteItemService
{
    use ServiceErrorHandler;
    use FavoriteItemConditionTrait;

    public function __construct(
        private FavoriteItemRepository $repository,
        private AddFavoriteItemAction $addFavoriteItemAction,
        private RemoveFavoriteItemAction $removeFavoriteItemAction,
        private RestoreFavoriteItemAction $restoreFavoriteItemAction
    ) {}

    /**
     * お気に入り商品を追加する
     *
     * @param int $userId ユーザーID
     * @param int $itemId 商品ID
     * @param int $siteId サイトID
     * @return FavoriteItem|null
     */
    public function add(int $userId, int $itemId, int $siteId): ?FavoriteItem
    {
        $data = new FavoriteItemData(
            userId: $userId,
            itemId: $itemId,
            siteId: $siteId
        );

        return $this->addFavoriteItemAction->execute($data);
    }

    /**
     * お気に入り商品を削除する
     *
     * @param int $userId ユーザーID
     * @param int $itemId 商品ID
     * @param int $siteId サイトID
     * @return bool
     */
    public function remove(int $userId, int $itemId, int $siteId): bool
    {
        $data = new FavoriteItemData(
            userId: $userId,
            itemId: $itemId,
            siteId: $siteId
        );

        return $this->removeFavoriteItemAction->execute($data);
    }

    /**
     * お気に入り商品を復元する
     *
     * @param int $userId ユーザーID
     * @param int $itemId 商品ID
     * @param int $siteId サイトID
     * @return FavoriteItem
     */
    public function restore(int $userId, int $itemId, int $siteId): FavoriteItem
    {
        $data = new FavoriteItemData(
            userId: $userId,
            itemId: $itemId,
            siteId: $siteId
        );

        return $this->restoreFavoriteItemAction->execute($data);
    }

    /**
     * ユーザーのお気に入り商品一覧を取得する
     *
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @param array $orderBy ソート条件
     * @param array $with イーガーロードするリレーション
     * @return Collection|null
     */
    public function getUserFavorites(
        int $userId,
        int $siteId,
        array $orderBy = ['created_at' => 'desc'],
        array $with = []
    ): ?Collection {
        return $this->tryCatchWrapper(
            function () use ($userId, $siteId, $orderBy, $with) {
                $criteria = new FavoriteItemSearchCriteria(
                    userId: $userId,
                    siteId: $siteId,
                    orderBy: $orderBy,
                    with: $with
                );

                return $this->repository->findBy(
                    conditions: $criteria->getConditions(),
                    with: $criteria->with,
                    orderBy: $criteria->orderBy
                );
            },
            'ユーザーのお気に入り商品一覧の取得に失敗しました',
            ['user_id' => $userId, 'site_id' => $siteId]
        );
    }
}
