<?php
/**
 * 商品カテゴリの更新サービス
 */
namespace App\Services\ItemCategory\Customer;

use App\Models\ItemCategory;
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
     * 商品カテゴリを更新する
     *
     * @param string $categoryCode
     * @param array $data
     * @return bool|null
     */
    public function update(string $categoryCode, array $data)
    {
        Log::info("Updating item category with code: $categoryCode");
        return $this->tryCatchWrapper(function () use ($categoryCode, $data) {
            $itemCategory = ItemCategory::where('category_code', $categoryCode)->first();

            if ($itemCategory) {
                return $itemCategory->update($data);
            }

            Log::error("Item category with code $categoryCode not found.");
            return false;
        }, '商品カテゴリの更新に失敗しました');
    }
}
