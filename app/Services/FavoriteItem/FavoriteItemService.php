<?php

declare(strict_types=1);

namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Repositories\FavoriteItem\FavoriteItemRepository;
use App\Repositories\Item\ItemRepository;
use Illuminate\Support\Facades\Log;

class FavoriteItemService
{
    protected $favoriteItemRepository;
    protected $itemRepository;

    public function __construct(FavoriteItemRepository $favoriteItemRepository, ItemRepository $itemRepository)
    {
        $this->favoriteItemRepository = $favoriteItemRepository;
        $this->itemRepository = $itemRepository;
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
                    'item_id' => $itemId,
                    'site_id' => $siteId,
                ];

                $favoriteItem = $this->favoriteItemRepository->findBy(
                    conditions: $conditions,
                    containTrash: true
                )->first();

                // お気に入り商品が存在しない場合は、新規登録
                if (!$favoriteItem) {
                    return $this->favoriteItemRepository->create([
                        'user_id' => $userId,
                        'item_id' => $itemId,
                        'site_id' => $siteId,
                    ]);
                }

                // 削除済みの場合は復元する
                if ($favoriteItem->trashed()) {
                    $restoredItem = $this->favoriteItemRepository->restore($favoriteItem->id);
                    if (!$restoredItem) {
                        throw new \Exception('お気に入り商品の復元に失敗しました');
                    }
                    return $restoredItem;
                }

                return $favoriteItem;
            },
            'お気に入り商品の登録に失敗しました'
        );
    }

    /**
     * お気に入り商品を削除します
     *
     * @param int $userId ユーザーID
     * @param int $itemId 商品ID
     * @param int $siteId サイトID
     * @return bool
     */
    public function remove(int $userId, int $itemId, int $siteId): bool
    {
        return $this->tryCatchWrapper(
            function () use ($userId, $itemId, $siteId) {
                $favoriteItem = $this->favoriteItemRepository->findBy([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'site_id' => $siteId,
                ])->first();

                if (!$favoriteItem) {
                    return false;
                }

                // 削除済みの場合は既に削除されているとみなす
                if ($favoriteItem->trashed()) {
                    return true;
                }

                return $this->favoriteItemRepository->delete($favoriteItem->id);
            },
            'お気に入り商品の削除に失敗しました'
        );
    }

    /**
     * ユーザーのお気に入り商品一覧を取得
     *
     * @param int $userId ユーザーID
     * @param int $siteId サイトID
     * @param array<string, string> $orderBy ソート条件 ['column' => 'asc|desc']
     * @param array<string, string> $with イーガーロードするリレーション
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getUserFavorites(int $userId, int $siteId, array $orderBy = ['created_at' => 'desc'], array $with = ['item', 'site']): ?\Illuminate\Database\Eloquent\Collection
    {
        return $this->tryCatchWrapper(
            function () use ($userId, $siteId, $orderBy, $with) {
                $conditions = [
                    'user_id' => $userId,
                    'site_id' => $siteId,
                ];

                return $this->favoriteItemRepository->findBy(conditions: $conditions, with: $with, orderBy: $orderBy);
            },
            'ユーザーのお気に入り商品一覧の取得に失敗しました'
        );
    }


}

