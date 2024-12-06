<?php

namespace App\Services\Operator\Order\Update\OrderDetail;

/**
 * 注文詳細整形サービスクラス
 *
 * 注文詳細データの整形を提供します。
 */
class OrderDetailFormatterService
{
    /**
     * 注文詳細データを整形する
     *
     * @param array $data 注文詳細データ
     * @return array 整形済みデータ
     */
    public function format(array $data): array
    {
        // データ整形ロジックを実装
        // 例: $data['formatted_date'] = date('Y-m-d', strtotime($data['date']));
        return $data;
    }
}
