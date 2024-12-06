<?php

namespace App\Services\Operator\Order\Update\OrderDetail;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;

/**
 * 注文詳細更新サービスクラス
 *
 * 注文の詳細情報を更新するためのサービスを提供します。
 */
class OrderDetailUpdateService
{
    private OrderDetailValidationService $validationService;
    private OrderDetailFormatterService $formatterService;
    private OrderDetailPersistenceService $persistenceService;
    private OrderLogService $orderLogService;

    /**
     * コンストラクタ
     *
     * @param OrderDetailValidationService $validationService 注文詳細バリデーションサービス
     * @param OrderDetailFormatterService $formatterService 注文詳細整形サービス
     * @param OrderDetailPersistenceService $persistenceService 注文詳細永続化サービス
     * @param OrderLogService $orderLogService 注文ログサービス
     */
    public function __construct(
        OrderDetailValidationService $validationService,
        OrderDetailFormatterService $formatterService,
        OrderDetailPersistenceService $persistenceService,
        OrderLogService $orderLogService
    ) {
        $this->validationService = $validationService;
        $this->formatterService = $formatterService;
        $this->persistenceService = $persistenceService;
        $this->orderLogService = $orderLogService;
    }

    /**
     * 注文の詳細情報を更新する
     *
     * @param Order $order 注文オブジェクト
     * @param array $data 更新データ
     * @return void
     */
    public function update(Order $order, array $data): void
    {
        try {
            $this->validationService->validate($data);
            $formattedData = $this->formatterService->format($data);
            $this->persistenceService->persist($order, $formattedData);
        } catch (\Exception $e) {
            $this->orderLogService->logError('Order detail update failed: ' . $e->getMessage(), [
                'orderId' => $order->id,
                'data' => $data
            ]);
            throw $e;
        }
    }
}
