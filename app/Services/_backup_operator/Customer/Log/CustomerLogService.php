<?php

namespace App\Services\Operator\Customer\Log;

use Illuminate\Support\Facades\Log;

/**
 * 顧客ログサービスクラス
 *
 * このクラスは顧客関連のログ管理を提供します。
 */
class CustomerLogService
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
