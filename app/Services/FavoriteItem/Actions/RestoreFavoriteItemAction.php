<?php

namespace App\Services\FavoriteItem\Actions;

use App\Models\FavoriteItem;
use App\Services\FavoriteItem\DTOs\FavoriteItemData;
use App\Services\FavoriteItem\Exceptions\FavoriteItemException;
use App\Services\FavoriteItem\Traits\FavoriteItemConditionTrait;
use App\Services\ServiceErrorHandler;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use Illuminate\Support\Facades\Log;

class RestoreFavoriteItemAction
{
    use ServiceErrorHandler;
    use FavoriteItemConditionTrait;

    public function __construct(
        private FavoriteItemRepository $repository
    ) {}

    /**
     * お気に入り商品を復元する
     *
     * @param FavoriteItemData $data
     * @return FavoriteItem
     * @throws FavoriteItemException
     */
    public function execute(FavoriteItemData $data): FavoriteItem
    {
        return $this->tryCatchWrapper(
            function () use ($data) {
                Log::info("Restoring favorite item", $data->toArray());

                // 削除済みのお気に入り商品を検索
                $favoriteItem = $this->repository->findBy(
                    conditions: $data->getConditions(),
                    containTrash: true
                )->first();

                if (!$favoriteItem) {
                    throw FavoriteItemException::notFound($data->getConditions());
                }

                // 削除済みでない場合は現在のものを返す
                if (!$favoriteItem->trashed()) {
                    return $favoriteItem;
                }

                // 復元を実行
                $restored = $this->repository->restore($favoriteItem->id);
                if (!$restored) {
                    throw FavoriteItemException::restoreFailed($favoriteItem->id);
                }

                return $restored;
            },
            'お気に入り商品の復元に失敗しました',
            $data->toArray()
        );
    }
}
