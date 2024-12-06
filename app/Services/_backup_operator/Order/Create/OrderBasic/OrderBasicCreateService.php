<?php

namespace App\Services\Operator\Order\Create\OrderBasic;

use App\Models\Order;

/**
 * 注文基本作成サービスクラス
 *
 * 注文の基本情報を作成するためのサービスを提供します。
 */
class OrderBasicCreateService
{
    private OrderBasicValidationService $validationService;
    private OrderBasicFormatterService $formatterService;
    private OrderBasicPersistenceService $persistenceService;

    /**
     * コンストラクタ
     *
     * @param OrderBasicValidationService $validationService 注文基本バリデーションサービス
     * @param OrderBasicFormatterService $formatterService 注文基本整形サービス
     * @param OrderBasicPersistenceService $persistenceService 注文基本永続化サービス
     */
    public function __construct(
        OrderBasicValidationService $validationService,
        OrderBasicFormatterService $formatterService,
        OrderBasicPersistenceService $persistenceService
    ) {
        $this->validationService = $validationService;
        $this->formatterService = $formatterService;
        $this->persistenceService = $persistenceService;
    }

    /**
     * 注文の基本情報を作成する
     *
     * @param array $data 作成データ
     * @return Order
     */
    public function create(array $data): Order
    {
        $this->validationService->validate($data);
        $formattedData = $this->formatterService->format($data);
        return $this->persistenceService->persist($formattedData);
    }
}
