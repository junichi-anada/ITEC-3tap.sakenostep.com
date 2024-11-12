<?php

namespace App\Services\Operator\Order\Delete;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;
use App\Services\Operator\Order\Transaction\OrderTransactionService;
use App\Services\Operator\Order\Delete\OrderBasic\OrderBasicDeleteService;
use App\Services\Operator\Order\Delete\OrderDetail\OrderDetailDeleteService;

/**
 * 注文削除サービスクラス
 *
 * このクラスは注文情報を削除するためのサービスを提供します。
 */
class OrderDeleteService
{
    private OrderBasicDeleteService $orderBasicDeleteService;
    private OrderDetailDeleteService $orderDetailDeleteService;
    private OrderLogService $orderLogService;
    private OrderTransactionService $orderTransactionService;

    public function __construct(
        OrderBasicDeleteService $orderBasicDeleteService,
        OrderDetailDeleteService $orderDetailDeleteService,
        OrderLogService $orderLogService,
        OrderTransactionService $orderTransactionService
    ) {
        $this->orderBasicDeleteService = $orderBasicDeleteService;
        $this->orderDetailDeleteService = $orderDetailDeleteService;
        $this->orderLogService = $orderLogService;
        $this->orderTransactionService = $orderTransactionService;
    }

    /**
     * 注文を削除する
     *
     * @param string $orderCode 注文コード
     * @return array 削除結果
     */
    public function deleteOrder(string $orderCode): array
    {
        try {
            return $this->orderTransactionService->execute(function () use ($orderCode) {
                $order = Order::where('order_code', $orderCode)->first();

                if (!$order) {
                    throw new \Exception('注文情報が見つかりません。');
                }

                $this->orderDetailDeleteService->delete($order);
                $this->orderBasicDeleteService->delete($order);

                return ['message' => 'success'];
            });
        } catch (\Exception $e) {
            $this->orderLogService->logError('Order deletion failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}
