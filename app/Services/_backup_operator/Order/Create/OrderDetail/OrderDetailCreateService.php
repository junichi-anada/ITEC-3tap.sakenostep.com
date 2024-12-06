<?php

namespace App\Services\Operator\Order\Create\OrderDetail;

use App\Models\Order;

/**
 * 注文詳細作成サービスクラス
 *
 * 注文の詳細情報を作成するためのサービスを提供します。
 */
class OrderDetailCreateService
{
    private OrderDetailValidationService $validationService;
    private OrderDetailFormatterService $formatterService;
    private OrderDetailPersistenceService $persistenceService;

    /**
     * コンストラクタ
     *
     * @param OrderDetailValidationService $validationService 注文詳細バリデーションサービス
     * @param OrderDetailFormatterService $formatterService 注文詳細整形サービス
     * @param OrderDetailPersistenceService $persistenceService 注文詳細永続化サービス
     */
    public function __construct(
        OrderDetailValidationService $validationService,
        OrderDetailFormatterService $formatterService,
        OrderDetailPersistenceService $persistenceService
    ) {
        $this->validationService = $validationService;
        $this->formatterService = $formatterService;
        $this->persistenceService = $persistenceService;
    }

    /**
     * 注文の詳細情報を作成する
     *
     * @param Order $order 注文オブジェクト
     * @param array $data 作成データ
     * @return void
     */
    public function create(Order $order, array $data): void
    {
        $this->validationService->validate($data);
        $formattedData = $this->formatterService->format($data);
        $this->persistenceService->persist($order, $formattedData);
    }
}
