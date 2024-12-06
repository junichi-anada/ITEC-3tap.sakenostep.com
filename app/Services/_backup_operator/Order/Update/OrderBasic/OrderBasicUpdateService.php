<?php

namespace App\Services\Operator\Order\Update\OrderBasic;

use App\Models\Order;
use App\Services\Operator\Order\Log\OrderLogService;

/**
 * 注文基本更新サービスクラス
 *
 * 注文の基本情報を更新するためのサービスを提供します。
 */
class OrderBasicUpdateService
{
    private OrderBasicValidationService $validationService;
    private OrderBasicFormatterService $formatterService;
    private OrderBasicPersistenceService $persistenceService;
    private OrderLogService $orderLogService;

    /**
     * コンストラクタ
     *
     * @param OrderBasicValidationService $validationService 注文基本バリデーションサービス
     * @param OrderBasicFormatterService $formatterService 注文基本整形サービス
     * @param OrderBasicPersistenceService $persistenceService 注文基本永続化サービス
     * @param OrderLogService $orderLogService 注文ログサービス
     */
    public function __construct(
        OrderBasicValidationService $validationService,
        OrderBasicFormatterService $formatterService,
        OrderBasicPersistenceService $persistenceService,
        OrderLogService $orderLogService
    ) {
        $this->validationService = $validationService;
        $this->formatterService = $formatterService;
        $this->persistenceService = $persistenceService;
        $this->orderLogService = $orderLogService;
    }

    /**
     * 注文の基本情報を更新する
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
            $this->orderLogService->logError('Order basic update failed: ' . $e->getMessage(), [
                'orderId' => $order->id,
                'data' => $data
            ]);
            throw $e;
        }
    }
}
