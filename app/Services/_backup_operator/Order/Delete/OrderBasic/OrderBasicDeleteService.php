<?php

namespace App\Services\Operator\Order\Delete\OrderBasic;

use App\Models\Order;

/**
 * 注文基本削除サービスクラス
 *
 * 注文の基本情報を削除するためのサービスを提供します。
 */
class OrderBasicDeleteService
{
    private OrderBasicValidationService $validationService;
    private OrderBasicPersistenceService $persistenceService;

    /**
     * コンストラクタ
     *
     * @param OrderBasicValidationService $validationService 注文基本バリデーションサービス
     * @param OrderBasicPersistenceService $persistenceService 注文基本永続化サービス
     */
    public function __construct(
        OrderBasicValidationService $validationService,
        OrderBasicPersistenceService $persistenceService
    ) {
        $this->validationService = $validationService;
        $this->persistenceService = $persistenceService;
    }

    /**
     * 注文の基本情報を削除する
     *
     * @param Order $order 注文オブジェクト
     * @return void
     */
    public function delete(Order $order): void
    {
        $this->validationService->validate($order);
        $this->persistenceService->delete($order);
    }
}
