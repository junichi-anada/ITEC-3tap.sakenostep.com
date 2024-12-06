<?php

namespace App\Services\Operator\Order\Create\OrderBasic;

/**
 * 注文基本整形サービスクラス
 *
 * 注文基本データの整形を提供します。
 */
class OrderBasicFormatterService
{
    /**
     * 注文基本データを整形する
     *
     * @param array $data 注文基本データ
     * @return array 整形済みデータ
     */
    public function format(array $data): array
    {
        // データ整形ロジックを実装
        // 例: $data['formatted_date'] = date('Y-m-d', strtotime($data['date']));
        return $data;
    }
}
