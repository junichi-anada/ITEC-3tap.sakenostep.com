<?php

namespace App\Services\FavoriteItem\Actions;

use App\Models\FavoriteItem;
use App\Services\FavoriteItem\DTOs\FavoriteItemData;
use App\Services\FavoriteItem\Exceptions\FavoriteItemException;
use App\Services\FavoriteItem\Traits\FavoriteItemConditionTrait;
use App\Services\ServiceErrorHandler;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use Illuminate\Support\Facades\Log;

class AddFavoriteItemAction
{
    use ServiceErrorHandler;
    use FavoriteItemConditionTrait;

    public function __construct(
        private FavoriteItemRepository $repository
    ) {}

    /**
     * お気に入り商品を追加する
     *
     * @param FavoriteItemData $data
     * @return FavoriteItem
     * @throws FavoriteItemException
     */
    public function execute(FavoriteItemData $data): FavoriteItem
    {
        return $this->tryCatchWrapper(
            function () use ($data) {
                Log::info("Adding favorite item", $data->toArray());

                // 既存のお気に入り商品を検索（削除済みを含む）
                $favoriteItem = $this->repository->findBy(
                    conditions: $data->getConditions(),
                    containTrash: true
                )->first();

                if (!$favoriteItem) {
                    Log::info('お気に入り商品が存在しないため作成します。');
                    return $this->createFavoriteItem($data);
                }

                // 削除済みの場合は復元
                if ($favoriteItem->trashed()) {
                    Log::info("Restoring favorite item ID: {$favoriteItem->id}");
                    $restored = $this->repository->restore($favoriteItem->id);
                    if (!$restored) {
                        throw FavoriteItemException::restoreFailed($favoriteItem->id);
                    }
                    return $restored;
                }

                return $favoriteItem;
            },
            'お気に入り商品の追加に失敗しました',
            $data->toArray()
        );
    }

    /**
     * お気に入り商品を作成する
     *
     * @param FavoriteItemData $data
     * @return FavoriteItem
     * @throws FavoriteItemException
     */
    private function createFavoriteItem(FavoriteItemData $data): FavoriteItem
    {
        $favoriteItem = $this->repository->create($data->toArray());
        
        if (!$favoriteItem) {
            throw FavoriteItemException::createFailed($data->toArray());
        }
        
        return $favoriteItem;
    }
}
