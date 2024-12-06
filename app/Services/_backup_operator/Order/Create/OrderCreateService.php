<?php

namespace App\Services\Operator\Order\Create;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;
use App\Services\Operator\Order\Transaction\OrderTransactionService;
use App\Services\Operator\Order\Create\OrderBasic\OrderBasicCreateService;
use App\Services\Operator\Order\Create\OrderDetail\OrderDetailCreateService;

/**
 * 注文登録サービスクラス
 *
 * このクラスは新しい注文を登録するためのサービスを提供します。
 */
class OrderCreateService
{
    private OrderBasicCreateService $orderBasicCreateService;
    private OrderDetailCreateService $orderDetailCreateService;
    private OrderLogService $orderLogService;
    private OrderTransactionService $orderTransactionService;

    public function __construct(
        OrderBasicCreateService $orderBasicCreateService,
        OrderDetailCreateService $orderDetailCreateService,
        OrderLogService $orderLogService,
        OrderTransactionService $orderTransactionService
    ) {
        $this->orderBasicCreateService = $orderBasicCreateService;
        $this->orderDetailCreateService = $orderDetailCreateService;
        $this->orderLogService = $orderLogService;
        $this->orderTransactionService = $orderTransactionService;
    }

    /**
     * 注文を登録する
     *
     * @param array $data 注文データ
     * @return array 登録結果
     */
    public function createOrder(array $data): array
    {
        try {
            return $this->orderTransactionService->execute(function () use ($data) {
                $order = $this->orderBasicCreateService->create($data['basic']);

                if (!empty($data['details'])) {
                    $this->orderDetailCreateService->create($order, $data['details']);
                }

                return ['message' => 'success', 'order_id' => $order->id];
            });
        } catch (\Exception $e) {
            $this->orderLogService->logError('Order registration failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}
