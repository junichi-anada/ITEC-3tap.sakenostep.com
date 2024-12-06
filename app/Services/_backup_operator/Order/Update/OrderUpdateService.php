<?php

namespace App\Services\Operator\Order\Update;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;
use App\Services\Operator\Order\Transaction\OrderTransactionService;
use App\Services\Operator\Order\Update\OrderBasic\OrderBasicUpdateService;
use App\Services\Operator\Order\Update\OrderDetail\OrderDetailUpdateService;

/**
 * 注文更新サービスクラス
 *
 * このクラスは注文情報を更新するためのサービスを提供します。
 */
class OrderUpdateService
{
    private OrderBasicUpdateService $orderBasicUpdateService;
    private OrderDetailUpdateService $orderDetailUpdateService;
    private OrderLogService $orderLogService;
    private OrderTransactionService $orderTransactionService;

    /**
     * コンストラクタ
     *
     * @param OrderBasicUpdateService $orderBasicUpdateService 注文基本更新サービス
     * @param OrderDetailUpdateService $orderDetailUpdateService 注文詳細更新サービス
     * @param OrderLogService $orderLogService 注文ログサービス
     * @param OrderTransactionService $orderTransactionService 注文トランザクションサービス
     */
    public function __construct(
        OrderBasicUpdateService $orderBasicUpdateService,
        OrderDetailUpdateService $orderDetailUpdateService,
        OrderLogService $orderLogService,
        OrderTransactionService $orderTransactionService
    ) {
        $this->orderBasicUpdateService = $orderBasicUpdateService;
        $this->orderDetailUpdateService = $orderDetailUpdateService;
        $this->orderLogService = $orderLogService;
        $this->orderTransactionService = $orderTransactionService;
    }

    /**
     * 注文を更新する
     *
     * @param string $orderCode 注文コード
     * @param array $data 更新データ
     * @return array 更新結果
     */
    public function updateOrder(string $orderCode, array $data): array
    {
        try {
            return $this->orderTransactionService->execute(function () use ($orderCode, $data) {
                $order = Order::where('order_code', $orderCode)->first();

                if (!$order) {
                    throw new \Exception('注文情報が見つかりません。');
                }

                $this->orderBasicUpdateService->update($order, $data['basic']);
                $this->orderDetailUpdateService->update($order, $data['details']);

                return ['message' => 'success'];
            });
        } catch (\Exception $e) {
            $this->orderLogService->logError('Order update failed: ' . $e->getMessage(), [
                'orderCode' => $orderCode,
                'data' => $data
            ]);
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}
