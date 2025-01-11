<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 基底サービスクラス
 *
 * 全てのサービスクラスの基底となるクラスです。
 * 共通のユーティリティメソッドやエラーハンドリング機能を提供します。
 */
abstract class BaseService
{
    /**
     * エラーメッセージ
     *
     * @var string|null
     */
    protected ?string $error = null;

    /**
     * エラーメッセージを設定
     *
     * @param string $message エラーメッセージ
     * @return void
     */
    protected function setError(string $message): void
    {
        $this->error = $message;
        Log::error($message);
    }

    /**
     * エラーメッセージを取得
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * エラーの有無を確認
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return !is_null($this->error);
    }

    /**
     * 例外処理を共通化するためのラッパーメソッド
     *
     * @param \Closure $callback 実行する処理
     * @param string $errorMessage エラー時のメッセージ
     * @return mixed
     */
    protected function tryCatchWrapper(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            $this->setError($errorMessage . ': ' . $e->getMessage());
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
