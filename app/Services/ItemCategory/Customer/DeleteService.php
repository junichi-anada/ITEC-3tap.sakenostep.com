<?php
/**
 * 商品カテゴリの削除サービス
 */
namespace App\Services\ItemCategory\Customer;

use App\Models\ItemCategory;
use Illuminate\Support\Facades\Log;

class DeleteService
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
     * 商品カテゴリを削除する
     *
     * @param string $categoryCode
     * @return bool|null
     */
    public function delete(string $categoryCode)
    {
        Log::info("Deleting item category with code: $categoryCode");
        return $this->tryCatchWrapper(function () use ($categoryCode) {
            $itemCategory = ItemCategory::where('category_code', $categoryCode)->first();

            if ($itemCategory) {
                return $itemCategory->delete();
            }

            Log::error("Item category with code $categoryCode not found.");
            return false;
        }, '商品カテゴリの削除に失敗しました');
    }
}
