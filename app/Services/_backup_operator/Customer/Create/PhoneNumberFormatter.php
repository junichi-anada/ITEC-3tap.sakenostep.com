<?php

namespace App\Services\Operator\Customer\Create;

/**
 * 電話番号フォーマットユーティリティクラス
 *
 * このクラスは電話番号をフォーマットするためのユーティリティを提供します。
 */
final class PhoneNumberFormatter
{
    /**
     * 電話番号をフォーマットする
     *
     * @param string $phone 電話番号
     * @return string フォーマット済み電話番号
     * @throws \Exception フォーマットに失敗した場合
     */
    public function formatPhoneNumber(string $phone): string
    {
        try {
            $formattedPhone = mb_convert_kana($phone, 'a');
            return str_replace('-', '', $formattedPhone);
        } catch (\Exception $e) {
            throw new \Exception('電話番号のフォーマットに失敗しました。');
        }
    }
}
