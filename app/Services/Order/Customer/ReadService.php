<?php
/**
 * 注文基本データ取得サービス
 */
namespace App\Services\Order\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

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
     * 注文コードに基づいて注文基本データを取得する
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
     * ユーザーIDに���づいて注文基本データを取得する
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
     * ユーザーIDに基づいて注文基本データを取得します。
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
     * ユーザーIDとサイトIDに基づいて注文基本データを取得する
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

    /**
     * 指定されたユーザーIDと注文IDに基づいて注文基本データを取得
     *
     * @param int $orderId
     * @param int $userId
     * @return Order|null
     */
    public function getOrderByIdAndUserId(int $orderId, int $userId): ?Order
    {
        return Order::where('id', $orderId)
                    ->where('user_id', $userId)
                    ->first();
    }

    /**
     * ユーザーIDとサイトIDに基づいて未発注の注文基本データを取得
     *
     * @param int $userId
     * @param int $siteId
     * @return Order|null
     */
    public function getUnorderedByUserIdAndSiteId(int $userId, int $siteId): ?Order
    {
        try {
            return Order::where('user_id', $userId)
                        ->where('site_id', $siteId)
                        ->whereNull('ordered_at')
                        ->first();
        } catch (\Exception $e) {
            Log::error('未発注の注文の取得に失敗しました: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ユーザーIDとサイトIDに基づいて発注済みの注文基本データを取得
     *
     * @param int $userId
     * @param int $siteId
     * @return Collection
     */
    public function getOrderedByUserIdAndSiteId(int $userId, int $siteId): Collection
    {
        try {
            return Order::where('user_id', $userId)
                        ->where('site_id', $siteId)
                        ->whereNotNull('ordered_at')
                        ->get();
        } catch (\Exception $e) {
            Log::error('発注済みの注文の取得に失敗しました: ' . $e->getMessage());
            return collect(); // 空のコレクションを返す
        }
    }
}



