<?php

namespace App\Services\Operator\Order\Log;

use Illuminate\Support\Facades\Log;

/**
 * 注文関連ログサービスクラス
 *
 * このクラスは注文関連の操作のログを記録するためのサービスを提供します。
 */
class OrderLogService
{
    /**
     * エラーログを記録
     *
     * @param string $message エラーメッセージ
     * @param array $context 追加のコンテキスト情報
     * @return void
     */
    public function logError(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * 情報ログを記録
     *
     * @param string $message 情報メッセージ
     * @param array $context 追加のコンテキスト情報
     * @return void
     */
    public function logInfo(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }
}
