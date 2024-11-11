<?php
/**
 *
 */
namespace App\Services\FavoriteItem\Customer;

use App\Models\FavoriteItem;
use App\Services\Item\Customer\ReadService as ItemReadService;
use Illuminate\Support\Facades\Log;

class DeleteService
{
    protected $itemReadService;

    public function __construct(ItemReadService $itemReadService)
    {
        $this->itemReadService = $itemReadService;
    }

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
     * @param string $itemCode
     * @param int $siteId
     * @return bool|null
     */
    public function softDelete(int $userId, string $itemCode, int $siteId): ?bool
    {
        Log::info("Deleting item with code: $itemCode for user: $userId on site: $siteId");
        return $this->tryCatchWrapper(function () use ($userId, $itemCode, $siteId) {
            // 商品コードから商品IDを取得
            $item = $this->itemReadService->getByItemCode($itemCode);
            if (!$item) {
                Log::error("Item with code $itemCode not found.");
                return false;
            }

            $favoriteItem = FavoriteItem::where([
                ['user_id', '=', $userId],
                ['item_id', '=', $item->id],
                ['site_id', '=', $siteId]
            ])->first();

            return $favoriteItem ? $favoriteItem->delete() : false;
        }, 'お気に入り商品の削除に失敗しました');
    }
}

