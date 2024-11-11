<?php
/**
 * 注文詳細の削除サービス
 */
namespace App\Services\OrderDetail\Customer;

use App\Models\OrderDetail;
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
     * 注文詳細を削除する
     *
     * @param int $orderDetailId
     * @return bool|null
     */
    public function delete(int $orderDetailId)
    {
        Log::info("Deleting order detail with ID: $orderDetailId");
        return $this->tryCatchWrapper(function () use ($orderDetailId) {
            $orderDetail = OrderDetail::find($orderDetailId);

            if ($orderDetail) {
                return $orderDetail->delete();
            }

            Log::error("Order detail with ID $orderDetailId not found.");
            return false;
        }, '注文詳細の削除に失敗しました');
    }

    /**
     * 指定された条件に基づいて注文詳細をソフトデリートする
     *
     * @param int $userId
     * @param int $siteId
     * @param int $itemId
     * @return bool
     */
    public function softDeleteItemFromUnorderedDetails($userId, $siteId, $itemId)
    {
        Log::info("Soft deleting order detail for user: $userId, site: $siteId, item: $itemId");

        $orderDetail = OrderDetail::whereHas('order', function ($query) use ($userId, $siteId) {
            $query->where('user_id', $userId)
                  ->where('site_id', $siteId)
                  ->whereNull('ordered_at');
        })->where('item_id', $itemId)->first();

        if ($orderDetail) {
            $orderDetail->delete();
            return true;
        }

        Log::error("Order detail not found for user: $userId, site: $siteId, item: $itemId");
        return false;
    }
}

