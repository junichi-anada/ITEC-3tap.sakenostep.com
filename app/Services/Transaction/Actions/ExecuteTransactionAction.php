<?php

namespace App\Services\Transaction\Actions;

use App\Services\Transaction\Exceptions\TransactionException;
use App\Services\Transaction\Traits\TransactionHandlerTrait;
use Illuminate\Support\Facades\Log;

class ExecuteTransactionAction
{
    use TransactionHandlerTrait;

    /**
     * トランザクション内でコールバックを実行する
     *
     * @param callable $callback 実行するコールバック関数
     * @param string $operation 操作の説明（ログ用）
     * @param array $context コンテキスト情報（ログ用）
     * @return mixed コールバックの戻り値
     * @throws TransactionException
     */
    public function execute(callable $callback, string $operation, array $context = []): mixed
    {
        Log::info("Starting transaction operation: {$operation}", $context);

        return $this->executeWithTransaction(
            callback: $callback,
            errorMessage: "トランザクション処理 '{$operation}' の実行に失敗しました",
            context: $context
        );
    }

    /**
     * 複数のコールバックをトランザクション内で順次実行する
     *
     * @param array $callbacks 実行するコールバック関数の配列
     * @param string $operation 操作の説明（ログ用）
     * @param array $context コンテキスト情報（ログ用）
     * @return array 各コールバックの戻り値の配列
     * @throws TransactionException
     */
    public function executeMultiple(array $callbacks, string $operation, array $context = []): array
    {
        Log::info("Starting multiple transaction operations: {$operation}", $context);

        return $this->executeWithTransaction(
            callback: function () use ($callbacks) {
                $results = [];
                foreach ($callbacks as $index => $callback) {
                    $results[$index] = $callback();
                }
                return $results;
            },
            errorMessage: "複数のトランザクション処理 '{$operation}' の実行に失敗しました",
            context: $context
        );
    }

    /**
     * トランザクションを手動制御するためのコンテキストを作成
     *
     * @param callable $callback
     * @return mixed
     * @throws TransactionException
     */
    public function withManualControl(callable $callback): mixed
    {
        try {
            $this->beginTransaction();
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e instanceof TransactionException ? $e : TransactionException::executionFailed('manual', $e);
        }
    }
}
