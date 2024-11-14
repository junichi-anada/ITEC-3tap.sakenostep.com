<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 電話番号バリデーションルールクラス
 *
 * このクラスは電話番号のフォーマットを検証するためのルールを提供します。
 */
class PhoneNumber implements Rule
{
    /**
     * バリデーションルールを通過するかどうかを判断
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 2つの正規表現パターンを許可
        return preg_match('/^\d{2,4}-\d{1,4}-\d{4}$/', $value) || preg_match('/^\d{4}-\d{4}$/', $value);
    }

    /**
     * バリデーションエラーメッセージを取得
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute format is invalid. It should be like 03-1234-5678 or 1234-5678.';
    }
}
