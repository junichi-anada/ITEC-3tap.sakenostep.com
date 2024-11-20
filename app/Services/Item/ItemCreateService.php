<?php
/**
 * 商品の作成サービス
 */
namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class ItemCreateService
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
     * 新しい商品を作成する
     *
     * @param array $data
     * @return Item|null
     */
    public function create(array $data)
    {
        Log::info("Creating new item with data: " . json_encode($data));
        return $this->tryCatchWrapper(function () use ($data) {
            return Item::create($data);
        }, '商品の作成に失敗しました');
    }
}
