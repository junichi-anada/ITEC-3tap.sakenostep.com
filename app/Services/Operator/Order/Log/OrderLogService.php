<?php

namespace App\Services\Operator\Order\Log;

use Illuminate\Support\Facades\Log;

/**
 * 注文ログサービスクラス
 *
 * このクラスは注文関連のログ管理を提供します。
 */
class OrderLogService
{
    /**
     * エラーログを記録する
     *
     * @param string $message エラーメッセージ
     * @param array $context コンテキスト情報
     * @return void
     */
    public function logError(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }
}
