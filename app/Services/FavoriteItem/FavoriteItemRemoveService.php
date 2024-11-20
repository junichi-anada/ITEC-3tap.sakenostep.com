<?php
/**
 *
 */
namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Repositories\FavoriteItemRepository;
use Illuminate\Support\Facades\Log;

class FavoriteItemRemoveService
{

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("Error: $errorMessage - " . $e->getMessage());
            return null;
        }
    }

    /**
     * ユーザーのお気に入り商品をソフトデリートで削除する
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @return int
     */
    public function remove(int $userId, string $itemId, int $siteId): ?int
    {
        return $this->tryCatchWrapper(
            function () use ($userId, $itemId, $siteId) {
                $result = 0;
                $conditions = [
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'site_id' => $siteId,
                ];
                $favoriteItem = $this->favoriteItemRepository->findBy($conditions);
                if ($favoriteItem) {
                    $result = $favoriteItem->delete();
                }
                return $result;
            },
            'お気に入り商品の削除に失敗しました'
        );
    }
}

