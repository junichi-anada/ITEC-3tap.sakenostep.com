<?php
/**
 * 顧客向けサービスのカテゴリ情報取得サービス
 * 
 * 顧客向けのカテゴリ情報取得に関するサービスクラスです。
 * カテゴリ情報の取得処理を行います。
 */
namespace App\Services\ItemCategory\Customer;

use App\Models\ItemCategory;
use Illuminate\Support\Facades\Log;

class ReadService
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
     * カテゴリリストを取得する
     *
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListBySiteId($siteId)
    {
        return $this->tryCatchWrapper(function () use ($siteId) {
            return ItemCategory::where('site_id', $siteId)
                               ->orderBy('priority', 'asc')
                               ->get();
        }, 'カテゴリリストの取得に失敗しました');
    }

    /**
     * カテゴリコードによるカテゴリ情報の取得
     *
     * @param int $siteId
     * @param string $categoryCode
     * @return ItemCategory|null
     */
    public function getByCategoryCode($siteId, $categoryCode)
    {
        return $this->tryCatchWrapper(function () use ($siteId, $categoryCode) {
            return ItemCategory::where('site_id', $siteId)
                               ->where('category_code', $categoryCode)
                               ->first();
        }, 'カテゴリコードによるカテゴリ情報の取得に失敗しました');
    }
}
