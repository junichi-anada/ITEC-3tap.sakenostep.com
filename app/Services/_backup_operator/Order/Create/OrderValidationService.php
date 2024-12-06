<?php

namespace App\Services\Operator\Order\Create;

/**
 * 注文バリデーションサービスクラス
 *
 * このクラスは注文データのバリデーションを提供します。
 */
class OrderValidationService
{
    /**
     * 注文データをバリデートする
     *
     * @param array $data 注文データ
     * @return void
     * @throws \Exception バリデーションに失敗した場合
     */
    public function validate(array $data): void
    {
        // バリデーションロジックを実装
        // 例: if (empty($data['order_code'])) { throw new \Exception('注文コードが必要です。'); }
    }
}
