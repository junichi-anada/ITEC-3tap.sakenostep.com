<?php
/**
 * 注文情報取得サービス
 */
namespace App\Services\Order\Customer;

use App\Models\Order;
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
     * 注文コードに基づいて注文情報を取得する
     *
     * @param string $orderCode
     * @return Order|null
     */
    public function getByOrderCode($orderCode)
    {
        return $this->tryCatchWrapper(function () use ($orderCode) {
            return Order::where('order_code', $orderCode)->first();
        }, '注文情報の取得に失敗しました');
    }

    /**
     * ユーザーIDに基づいて注文リストを取得する
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListByUserId($userId)
    {
        return $this->tryCatchWrapper(function () use ($userId) {
            return Order::where('user_id', $userId)->get();
        }, '注文リストの取得に失敗しました');
    }

    /**
     * ユーザーIDに基づいて注文リストを取得します。
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByUserId($userId)
    {
        try {
            return Order::where('user_id', $userId)->get();
        } catch (\Exception $e) {
            Log::error('注文リストの取得に失敗しました: ' . $e->getMessage());
            return collect(); // 空のコレクションを返す
        }
    }

    /**
     * ユーザーIDとサイトIDに基づいて注文情報を取得する
     *
     * @param int $userId
     * @param int $siteId
     * @return Order|null
     */
    public function getByUserIdAndSiteId($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return Order::where('user_id', $userId)->where('site_id', $siteId)->first();
        }, '注文情報の取得に失敗しました');
    }
}



