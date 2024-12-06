<?php

namespace App\Services\Operator\Order\Create\OrderBasic;

use App\Models\Order;

/**
 * 注文基本永続化サービスクラス
 *
 * 注文基本データの永続化を提供します。
 */
class OrderBasicPersistenceService
{
    /**
     * 注文基本データを永続化する
     *
     * @param array $data 永続化するデータ
     * @return Order
     */
    public function persist(array $data): Order
    {
        // 永続化ロジックを実装
        return Order::create($data);
    }
}
