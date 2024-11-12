<?php

namespace App\Services\Operator\Order\Update\OrderBasic;

/**
 * 注文基本バリデーションサービスクラス
 *
 * 注文基本データのバリデーションを提供します。
 */
class OrderBasicValidationService
{
    /**
     * 注文基本データをバリデートする
     *
     * @param array $data 注文基本データ
     * @return void
     * @throws \Exception バリデーションに失敗した場合
     */
    public function validate(array $data): void
    {
        // バリデーションロジックを実装
        // 例: if (empty($data['order_code'])) { throw new \Exception('注文コードが必要です。'); }
    }
}
