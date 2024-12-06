<?php

namespace App\Services\Transaction\Traits;

use App\Services\Transaction\Exceptions\TransactionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait TransactionHandlerTrait
{
    /**
     * トランザクション処理を実行する
     *
     * @param callable $callback
     * @param string $errorMessage
     * @param array $context
     * @return mixed
     * @throws TransactionException
     */
    protected function executeWithTransaction(callable $callback, string $errorMessage, array $context = [])
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($errorMessage, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'context' => $context
            ]);
            throw new TransactionException($errorMessage, $e);
        }
    }

    /**
     * トランザクションを開始する
     *
     * @throws TransactionException
     */
    protected function beginTransaction(): void
    {
        try {
            DB::beginTransaction();
        } catch (Throwable $e) {
            throw new TransactionException('トランザクション開始に失敗しました', $e);
        }
    }

    /**
     * トランザクションをコミットする
     *
     * @throws TransactionException
     */
    protected function commit(): void
    {
        try {
            DB::commit();
        } catch (Throwable $e) {
            throw new TransactionException('トランザクションのコミットに失敗しました', $e);
        }
    }

    /**
     * トランザクションをロールバックする
     *
     * @throws TransactionException
     */
    protected function rollBack(): void
    {
        try {
            DB::rollBack();
        } catch (Throwable $e) {
            throw new TransactionException('トランザクションのロールバックに失敗しました', $e);
        }
    }
}
