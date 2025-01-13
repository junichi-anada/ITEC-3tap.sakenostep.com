<?php

namespace App\Services\Customer\Formatter;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * 顧客データフォーマッターサービス
 * 
 * 電話番号、パスワード、郵便番号のフォーマット処理を提供します。
 */
class CustomerDataFormatter
{
    /**
     * 電話番号をフォーマットする
     * 
     * @param string|null $phone 電話番号
     * @return string|null フォーマット済み電話番号
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // 00-0000形式かどうかを判定
        if (preg_match('/^\d{2}-\d{4}$/', $phone)) {
            return '0176-' . $phone;
        }

        return $phone;
    }

    /**
     * 電話番号からパスワードを生成する
     * 
     * @param string|null $phone 優先電話番号
     * @param string|null $phone2 第2電話番号
     * @param string|null $fax FAX番号
     * @return string ハッシュ化されたパスワード
     * @throws \Exception 有効な電話番号が存在しない場合
     */
    public function generatePasswordFromPhone(?string $phone, ?string $phone2 = null, ?string $fax = null): string
    {
        // 優先順位に従って電話番号を選択
        $targetPhone = $phone ?? $phone2 ?? $fax;

        if (empty($targetPhone)) {
            throw new \Exception('パスワード生成用の電話番号が存在しません。');
        }

        // 電話番号が「R」から始まるランダムパスワードの場合はそのまま返す
        if (str_starts_with($targetPhone, 'R')) {
            return Hash::make($targetPhone);
        }

        // 通常の電話番号の場合は数字のみを抽出
        // 全角を半角に変換してからハイフンと数字以外を除去
        $normalized = mb_convert_kana($targetPhone, 'a');  // 全角数字を半角に変換
        $numbers = preg_replace('/[-\s]/', '', $normalized);  // ハイフンとスペースを除去
        $numbers = preg_replace('/[^0-9]/', '', $numbers);   // 数字以外を除去

        // Log::info('電話番号から数字を抽出します', [
        //     'target_phone' => $targetPhone,
        //     'normalized' => $normalized,
        //     'numbers' => $numbers
        // ]);

        if (empty($numbers)) {
            throw new \Exception('電話番号から数字を抽出できませんでした。');
        }
        
        return Hash::make($numbers);
    }

    /**
     * 郵便番号をフォーマットする
     * 
     * @param string|null $postalCode 郵便番号
     * @return string|null フォーマット済み郵便番号
     */
    public function formatPostalCode(?string $postalCode): ?string
    {
        if (empty($postalCode)) {
            return null;
        }

        // 数字以外を除去
        $numbers = preg_replace('/[^0-9]/', '', $postalCode);
        
        // 6桁の場合、左に0を付加
        if (strlen($numbers) === 6) {
            $numbers = '0' . $numbers;
        }
        
        // 7桁未満の場合はnullを返す
        if (strlen($numbers) < 7) {
            return null;
        }

        // 000-0000形式に変換（先頭7桁のみ使用）
        return substr($numbers, 0, 3) . '-' . substr($numbers, 3, 4);
    }
} 