<?php

namespace App\Services\Operator\Order\Update\OrderDetail;

use App\Models\Order;

/**
 * 注文詳細永続化サービスクラス
 *
 * 注文詳細データの永続化を提供します。
 */
class OrderDetailPersistenceService
{
    /**
     * 注文詳細データを永続化する
     *
     * @param Order $order 注文オブジェクト
     * @param array $data 永続化するデータ
     * @return void
     */
    public function persist(Order $order, array $data): void
    {
        // 永続化ロジックを実装
        // 例: $order->details()->update($data);
    }
}
