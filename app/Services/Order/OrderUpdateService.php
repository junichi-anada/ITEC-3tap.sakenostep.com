<?php
/**
 * 注文の更新サービス
 */
namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderUpdateService
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
     * 注文を更新する
     *
     * @param string $orderCode
     * @param array $data
     * @return bool|null
     */
    public function update(string $orderCode, array $data)
    {
        Log::info("Updating order with code: $orderCode");
        return $this->tryCatchWrapper(function () use ($orderCode, $data) {
            $order = Order::where('order_code', $orderCode)->first();

            if ($order) {
                return $order->update($data);
            }

            Log::error("Order with code $orderCode not found.");
            return false;
        }, '注文の更新に失敗しました');
    }

    /**
     * 注文の日付を更新する
     *
     * @param int $userId
     * @param int $siteId
     * @return Order|null
     */
    public function updateOrderDate($userId, $siteId)
    {
        $order = Order::where('user_id', $userId)
                      ->where('site_id', $siteId)
                      ->whereNull('ordered_at')
                      ->first();

        if ($order) {
            $order->ordered_at = now();
            $order->save();
            return $order;
        }

        Log::error("Order not found for user: $userId, site: $siteId");
        return null;
    }
}

