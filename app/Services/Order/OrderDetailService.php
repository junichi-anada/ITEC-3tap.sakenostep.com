<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\ServiceErrorHandler;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderDetailService
{
    use ServiceErrorHandler;

    /**
     * 注文詳細情報を取得
     *
     * @param int $orderId
     * @return Order|null
     */
    public function execute(int $orderId): ?Order
    {
        try {
            return Order::with([
                'customer',
                'orderDetails.item'
            ])->findOrFail($orderId);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->setError('注文情報の取得に失敗しました。');
            return null;
        }
    }

    /**
     * 注文番号から注文詳細情報を取得
     *
     * @param string $orderNumber
     * @return Order|null
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        try {
            return Order::with([
                'customer',
                'orderDetails.item'
            ])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->setError('注文情報の取得に失敗しました。');
            return null;
        }
    }
}
