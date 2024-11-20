<?php

namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use Illuminate\Support\Facades\Log;

class FavoriteItemAddService
{
    protected $favoriteItemRepository;

    public function __construct(FavoriteItemRepository $favoriteItemRepository)
    {
        $this->favoriteItemRepository = $favoriteItemRepository;
    }

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper($callback, $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error($errorMessage . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * お気に入り商品追加
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @return FavoriteItem|null
     */
    public function add(int $userId, int $itemId, int $siteId): ?FavoriteItem
    {
        return $this->tryCatchWrapper(
            function () use ($userId, $itemId, $siteId) {

                // お気に入り商品を削除済みも含めて検索
                $conditions = [
                    'user_id' => $userId,
                    'site_id' => $siteId,
                ];
                $containTrash = true;

                $favoriteItems = $this->favoriteItemSearchRepository
                                    ->findBy($conditions, [], [], $containTrash);

                // 初めてのお気に入り登録の場合は、新規登録
                if ($favoriteItems->isEmpty()) {
                    return $this->favoriteItemRepository->create([
                        'user_id' => $userId,
                        'item_id' => $itemId,
                        'site_id' => $siteId,
                    ]);
                }

                // $favoriteItemsの中に$itemIdの商品があるかどうか再検索
                $conditions = [
                    'user_id' => $userId,
                    'site_id' => $siteId,
                    'item_id' => $itemId,
                ];
                $containTrash = true;

                $favoriteItems = $this->favoriteItemRepository
                                    ->findBy($conditions, [], [], $containTrash);

                // 該当なければ、新規登録する
                if (!$favoriteItem) {
                    return $this->favoriteItemRepository->create([
                        'user_id' => $userId,
                        'item_id' => $itemId,
                        'site_id' => $siteId,
                    ]);
                }

                // $favoriteItemが削除済みの場合は、復元する
                if ($favoriteItem && $favoriteItem->trashed()) {
                    return $this->favoriteItemRepository->restore($favoriteItem->id);
                }
            },
            'お気に入り商品の登録に失敗しました'
        );
    }

}

