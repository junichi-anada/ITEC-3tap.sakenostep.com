<?php

namespace App\Services\Transaction\Exceptions;

use Exception;
use Throwable;

class TransactionException extends Exception
{
    private ?Throwable $originalException;

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->originalException = $previous;
    }

    /**
     * 元の例外を取得する
     *
     * @return Throwable|null
     */
    public function getOriginalException(): ?Throwable
    {
        return $this->originalException;
    }

    /**
     * トランザクション開始エラー
     *
     * @param Throwable $e
     * @return self
     */
    public static function beginFailed(Throwable $e): self
    {
        return new self('トランザクションの開始に失敗しました', $e);
    }

    /**
     * トランザクションコミットエラー
     *
     * @param Throwable $e
     * @return self
     */
    public static function commitFailed(Throwable $e): self
    {
        return new self('トランザクションのコミットに失敗しました', $e);
    }

    /**
     * トランザクションロールバックエラー
     *
     * @param Throwable $e
     * @return self
     */
    public static function rollbackFailed(Throwable $e): self
    {
        return new self('トランザクションのロールバックに失敗しました', $e);
    }

    /**
     * トランザクション実行エラー
     *
     * @param string $operation
     * @param Throwable $e
     * @return self
     */
    public static function executionFailed(string $operation, Throwable $e): self
    {
        return new self("トランザクション処理 '{$operation}' の実行に失敗しました", $e);
    }
}
