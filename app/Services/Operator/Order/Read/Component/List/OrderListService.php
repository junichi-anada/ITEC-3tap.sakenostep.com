<?php

namespace App\Services\Operator\Order\Read\Component\List;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;

/**
 * 注文一覧取得サービスクラス
 *
 * このクラスは注文一覧を取得するためのサービスを提供します。
 */
class OrderListService
{
    private OrderLogService $orderLogService;
    private OrderListFormatterService $formatterService;

    public function __construct(OrderLogService $orderLogService, OrderListFormatterService $formatterService)
    {
        $this->orderLogService = $orderLogService;
        $this->formatterService = $formatterService;
    }

    /**
     * 注文一覧を取得
     *
     * @return \Illuminate\Database\Eloquent\Collection 注文のコレクション
     */
    public function getOrderList()
    {
        try {
            $orders = Order::all();
            return $this->formatterService->format($orders->toArray());
        } catch (\Exception $e) {
            $this->orderLogService->logError('Failed to retrieve order list: ' . $e->getMessage());
            return collect();
        }
    }
}
