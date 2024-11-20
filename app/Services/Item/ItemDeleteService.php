<?php
/**
 * 商品の削除サービス
 */
namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class ItemDeleteService
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
     * 商品を削除する
     *
     * @param string $itemCode
     * @return bool|null
     */
    public function delete(string $itemCode)
    {
        Log::info("Deleting item with code: $itemCode");
        return $this->tryCatchWrapper(function () use ($itemCode) {
            $item = Item::where('item_code', $itemCode)->first();

            if ($item) {
                return $item->delete();
            }

            Log::error("Item with code $itemCode not found.");
            return false;
        }, '商品の削除に失敗しました');
    }
}
