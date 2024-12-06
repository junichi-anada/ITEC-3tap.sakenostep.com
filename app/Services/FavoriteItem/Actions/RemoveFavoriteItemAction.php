<?php

namespace App\Services\FavoriteItem\Actions;

use App\Services\FavoriteItem\DTOs\FavoriteItemData;
use App\Services\FavoriteItem\Exceptions\FavoriteItemException;
use App\Services\FavoriteItem\Traits\FavoriteItemConditionTrait;
use App\Services\ServiceErrorHandler;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use Illuminate\Support\Facades\Log;

class RemoveFavoriteItemAction
{
    use ServiceErrorHandler;
    use FavoriteItemConditionTrait;

    public function __construct(
        private FavoriteItemRepository $repository
    ) {}

    /**
     * お気に入り商品を削除する
     *
     * @param FavoriteItemData $data
     * @return bool
     * @throws FavoriteItemException
     */
    public function execute(FavoriteItemData $data): bool
    {
        return $this->tryCatchWrapper(
            function () use ($data) {
                Log::info("Removing favorite item", $data->toArray());

                // お気に入り商品を検索
                $favoriteItem = $this->repository->findBy($data->getConditions())->first();

                if (!$favoriteItem) {
                    Log::info('お気に入り商品が見つかりません。', $data->toArray());
                    return false;
                }

                // 既に削除済みの場合は成功とみなす
                if ($favoriteItem->trashed()) {
                    return true;
                }

                $result = $this->repository->delete($favoriteItem->id);
                if (!$result) {
                    throw FavoriteItemException::deleteFailed($data->getConditions());
                }

                return $result;
            },
            'お気に入り商品の削除に失敗しました',
            $data->toArray()
        );
    }
}
