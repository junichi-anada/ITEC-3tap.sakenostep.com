<?php
/**
 * 注文詳細情報取得サービス
 */
namespace App\Services\OrderDetail\Customer;

use App\Models\OrderDetail;
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
     * 注文詳細IDに基づいて注文詳細情報を取得する
     *
     * @param int $orderDetailId
     * @return OrderDetail|null
     */
    public function getById($orderDetailId)
    {
        return $this->tryCatchWrapper(function () use ($orderDetailId) {
            return OrderDetail::find($orderDetailId);
        }, '注文詳細情報の取得に失敗しました');
    }

    /**
     * 注文IDに基づいて注文詳細リストを取得する
     *
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getListByOrderId($orderId)
    {
        return $this->tryCatchWrapper(function () use ($orderId) {
            return OrderDetail::where('order_id', $orderId)->get();
        }, '注文詳細リストの取得に失敗しました');
    }

    /**
     * ユーザーIDとサイトIDに基づいて未注文の商品リストを取得します。
     *
     * @param int $userId
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnorderedListByUserIdAndSiteId($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return OrderDetail::join('orders', 'order_details.order_id', '=', 'orders.id')
                              ->where('orders.user_id', $userId)
                              ->where('orders.site_id', $siteId)
                              ->whereNull('orders.ordered_at')
                              ->select('order_details.*')
                              ->get();
        }, '未注文の商品リストの取得に失敗しました');
    }

    /**
     * ユーザーIDとサイトIDに基づいて未注文の商品リストを取得し、関連するアイテム情報を含めます。
     *
     * @param int $userId
     * @param int $siteId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnorderedListWithItemsByUserIdAndSiteId($userId, $siteId)
    {
        return $this->tryCatchWrapper(function () use ($userId, $siteId) {
            return OrderDetail::join('orders', 'order_details.order_id', '=', 'orders.id')
                              ->where('orders.user_id', $userId)
                              ->where('orders.site_id', $siteId)
                              ->whereNull('orders.ordered_at')
                              ->with('item') // 必要に応じてリレーションをロード
                              ->select('order_details.*')
                              ->get();
        }, '未注文の商品リストとアイテム情報の取得に失敗しました');
    }

    /**
     * 指定された注文IDに基づいて注文詳細を取得
     *
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public function getDetailsByOrderId(int $orderId)
    {
        try {
            return OrderDetail::where('order_id', $orderId)->get();
        } catch (\Exception $e) {
            Log::error('注文詳細の取得に失敗しました: ' . $e->getMessage());
            return collect(); // 空のコレクションを返す
        }
    }
}
