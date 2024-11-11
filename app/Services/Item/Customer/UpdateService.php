<?php
/**
 * 商品の更新サービス
 */
namespace App\Services\Item\Customer;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class UpdateService
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
     * 商品を更新する
     *
     * @param string $itemCode
     * @param array $data
     * @return bool|null
     */
    public function update(string $itemCode, array $data)
    {
        Log::info("Updating item with code: $itemCode");
        return $this->tryCatchWrapper(function () use ($itemCode, $data) {
            $item = Item::where('item_code', $itemCode)->first();

            if ($item) {
                return $item->update($data);
            }

            Log::error("Item with code $itemCode not found.");
            return false;
        }, '商品の更新に失敗しました');
    }
}
