<?php

namespace App\Services\Operator\Order\Read\Component\Count;

use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

/**
 * 注文カウント条件バリデーションサービスクラス
 *
 * このクラスは注文カウントの条件をバリデーションするためのサービスを提供します。
 */
class OrderCountValidationService
{
    /**
     * カウント条件をバリデーション
     *
     * @param array $criteria カウント条件
     * @throws InvalidArgumentException バリデーション失敗時
     * @return void
     */
    public function validate(array $criteria): void
    {
        $validator = Validator::make($criteria, [
            'status' => 'required|string|in:all,pending,processing,completed,cancelled',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}
