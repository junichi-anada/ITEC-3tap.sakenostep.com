<?php

namespace App\Services\Operator\Order\Read\Component\Count;

/**
 * 注文数バリデーションサービスクラス
 *
 * 注文数をカウントする際のバリデーションを提供します。
 */
class OrderCountValidationService
{
    /**
     * 注文数カウントのバリデーションを行う
     *
     * @param array $criteria カウント条件
     * @return void
     * @throws \Exception バリデーションに失敗した場合
     */
    public function validate(array $criteria): void
    {
        // バリデーションロジックを実装
        // 例: if (empty($criteria['date'])) { throw new \Exception('日付が必要です。'); }
    }
}
