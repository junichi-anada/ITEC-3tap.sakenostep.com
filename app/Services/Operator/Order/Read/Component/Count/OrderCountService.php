<?php

namespace App\Services\Operator\Order\Read\Component\Count;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;

/**
 * 注文数カウントサービスクラス
 *
 * このクラスは注文数をカウントするためのサービスを提供します。
 */
class OrderCountService
{
    private OrderLogService $orderLogService;
    private OrderCountValidationService $validationService;

    public function __construct(OrderLogService $orderLogService, OrderCountValidationService $validationService)
    {
        $this->orderLogService = $orderLogService;
        $this->validationService = $validationService;
    }

    /**
     * 注文数を取得
     *
     * @param array $criteria カウント条件
     * @return int 注文数
     */
    public function getOrderCount(array $criteria): int
    {
        try {
            $this->validationService->validate($criteria);
            return Order::count();
        } catch (\Exception $e) {
            $this->orderLogService->logError('Failed to count orders: ' . $e->getMessage());
            return 0;
        }
    }
}
