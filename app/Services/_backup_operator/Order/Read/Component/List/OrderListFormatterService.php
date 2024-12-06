<?php

namespace App\Services\Operator\Order\Read\Component\List;

/**
 * 注文一覧整形サービスクラス
 *
 * 注文一覧データの整形を提供します。
 */
class OrderListFormatterService
{
    /**
     * 注文一覧データを整形する
     *
     * @param array $data 注文一覧データ
     * @return array 整形済みデータ
     */
    public function format(array $data): array
    {
        // データ整形ロジックを実装
        // 例: foreach ($data as &$order) { $order['formatted_date'] = date('Y-m-d', strtotime($order['date'])); }
        return $data;
    }
}
