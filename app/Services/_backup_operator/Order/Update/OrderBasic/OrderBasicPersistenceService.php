<?php

namespace App\Services\Operator\Order\Update\OrderBasic;

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
     * @param Order $order 注文オブジェクト
     * @param array $data 永続化するデータ
     * @return void
     */
    public function persist(Order $order, array $data): void
    {
        // 永続化ロジックを実装
        $order->update($data);
    }
}
