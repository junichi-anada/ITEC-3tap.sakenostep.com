<?php



namespace App\Services\FavoriteItem\Customer;

use App\Models\FavoriteItem;
use Illuminate\Support\Facades\Log;

class CreateService
{
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
     * ユーザーのお気に入り商品を登録する
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @return FavoriteItem|null
     */
    public function create($userId, $itemId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $itemId, $siteId) {
            return FavoriteItem::create([
                'user_id' => $userId,
                'item_id' => $itemId,
                'site_id' => $siteId,
            ]);
        }, 'お気に入り商品の登録に失敗しました');
    }
}

