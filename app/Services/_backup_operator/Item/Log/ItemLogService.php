<?php

namespace App\Services\Operator\Item\Log;

use Illuminate\Support\Facades\Log;

/**
 * アイテムログサービスクラス
 *
 * このクラスはアイテム関連のログ管理を提供します。
 */
class ItemLogService
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
