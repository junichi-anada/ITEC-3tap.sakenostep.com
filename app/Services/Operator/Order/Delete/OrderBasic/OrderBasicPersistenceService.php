<?php

namespace App\Services\Operator\Order\Delete\OrderBasic;

use App\Models\Order;

/**
 * 注文基本永続化サービスクラス
 *
 * 注文基本データの永続化を提供します。
 */
class OrderBasicPersistenceService
{
    /**
     * 注文基本データを削除する
     *
     * @param Order $order 注文オブジェクト
     * @return void
     */
    public function delete(Order $order): void
    {
        // 永続化ロジックを実装
        $order->delete();
    }
}
