<?php

namespace App\Services\Operator\Order\Delete\OrderBasic;

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
     * @param mixed $data 注文基本データ
     * @return void
     * @throws \Exception バリデーションに失敗した場合
     */
    public function validate($data): void
    {
        // バリデーションロジックを実装
        // 例: if (!$data->exists) { throw new \Exception('注文が存在しません。'); }
    }
}
