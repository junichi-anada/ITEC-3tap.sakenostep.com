<?php
/**
 * お気に入り商品情報取得サービス
 * 
 * お気に入り商品の情報取得に関するサービスクラスです。
 */
namespace App\Services\FavoriteItem\Customer;

use App\Models\FavoriteItem;
use App\Models\Item;
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
     * ユーザーのお気に入り商品リストを取得する
     *
     * @param int $userId
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListByUserAndSiteId($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return FavoriteItem::where('user_id', $userId)
                               ->where('site_id', $siteId)
                               ->with('item')
                               ->get();
        }, 'お気に入り商品リストの取得に失敗しました');
    }

    /**
     * ユーザーのお気に入り商品リストを取得する（商品情報も含む）
     *
     * @param int $userId
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListWithItemDetailsByUserAndSiteId($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return FavoriteItem::where('user_id', $userId)
                               ->where('site_id', $siteId)
                               ->with(['item' => function($query) {
                                   $query->select('id', 'item_code', 'name', 'description', 'unit_price');
                               }])
                               ->get();
        }, 'お気に入り商品リストと商品情報の取得に失敗しました');
    }

    /**
     * ユーザーのお気に入り商品のitem_idリストを取得する
     *
     * @param int $userId
     * @param int $siteId
     * @return array|null
     */
    public function getItemIdListByUserAndSiteId($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return FavoriteItem::where('user_id', $userId)
                            ->where('site_id', $siteId)
                            ->pluck('item_id')
                            ->all();
        }, 'お気に入り商品のitem_idリストの取得に失敗しました');
    }

    /**
     * ユーザーのお気に入り商品リストを取得する（ソフトデリートも含む）
     *
     * @param int $userId
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListByUserAndSiteIdWithTrashed($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return FavoriteItem::withTrashed()
                            ->where('user_id', $userId)
                            ->where('site_id', $siteId)
                            ->with('item')
                            ->get();
        }, 'ソフトデリートを含むお気に入り商品リストの取得に失敗しました');
    }

    /**
     * ユーザーIDと商品IDに基づいて、そのユーザーのお気に入り商品を取得する
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @return FavoriteItem|null
     */
    public function getByUserIdAndItemId($userId, $itemId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId, $itemId) {
            return FavoriteItem::where('user_id', $userId)
                            ->where('site_id', $siteId)
                            ->where('item_id', $itemId)
                            ->with('item')
                            ->first();
        }, '指定したユーザーのお気に入り商品の取得に失敗しました');
    }

    /**
     * ユーザーIDと商品IDに基づいて、そのユーザーのお気に入り商品を取得する（ソフトデリートも含む）
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @return FavoriteItem|null
     */
    public function getByUserIdAndItemIdWithTrashed($userId, $itemId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId, $itemId) {
            return FavoriteItem::withTrashed()
                            ->where('user_id', $userId)
                            ->where('site_id', $siteId)
                            ->where('item_id', $itemId)
                            ->with('item')
                            ->first();
        }, 'ソフトデリートを含む指定したユーザーのお気に入り商品の取得に失敗しました');
    }

    
}
