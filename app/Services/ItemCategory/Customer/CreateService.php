<?php
/**
 * 商品カテゴリの作成サービス
 */
namespace App\Services\ItemCategory\Customer;

use App\Models\ItemCategory;
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
     * 新しい商品カテゴリを作成する
     *
     * @param array $data
     * @return ItemCategory|null
     */
    public function create(array $data)
    {
        Log::info("Creating new item category with data: " . json_encode($data));
        return $this->tryCatchWrapper(function () use ($data) {
            return ItemCategory::create($data);
        }, '商品カテゴリの作成に失敗しました');
    }
}
