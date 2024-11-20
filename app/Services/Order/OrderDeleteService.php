<?php
/**
 * 注文の削除サービス
 */
namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderDeleteService
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
     * 注文を削除する
     *
     * @param string $orderCode
     * @return bool|null
     */
    public function delete(string $orderCode)
    {
        Log::info("Deleting order with code: $orderCode");
        return $this->tryCatchWrapper(function () use ($orderCode) {
            $order = Order::where('order_code', $orderCode)->first();

            if ($order) {
                return $order->delete();
            }

            Log::error("Order with code $orderCode not found.");
            return false;
        }, '注文の削除に失敗しました');
    }
}
