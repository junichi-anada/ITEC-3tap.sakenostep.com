<?php

namespace App\Services\Operator\Order\Delete\OrderDetail;

use App\Models\Order;

/**
 * 注文詳細削除サービスクラス
 *
 * 注文の詳細情報を削除するためのサービスを提供します。
 */
class OrderDetailDeleteService
{
    /**
     * 注文の詳細情報を削除する
     *
     * @param Order $order 注文オブジェクト
     * @return void
     */
    public function delete(Order $order): void
    {
        // 詳細情報の削除ロジックをここに実装
        // 例: $order->details()->delete();
    }
}
