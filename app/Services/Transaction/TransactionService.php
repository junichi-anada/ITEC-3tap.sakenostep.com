<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Services\Transaction\Actions\ExecuteTransactionAction;
use App\Services\Transaction\Exceptions\TransactionException;
use App\Services\Transaction\Traits\TransactionHandlerTrait;

/**
 * トランザクションサービス
 *
 * このクラスはトランザクション処理のファサードとして機能し、
 * 具体的な処理をExecuteTransactionActionに委譲します。
 */
final class TransactionService
{
    use TransactionHandlerTrait;

    public function __construct(
        private ExecuteTransactionAction $executeTransactionAction
    ) {}

    /**
     * トランザクション内でコールバックを実行する
     *
     * @param callable $callback 実行するコールバック関数
     * @param string $operation 操作の説明（ログ用）
     * @param array $context コンテキスト情報（ログ用）
     * @return mixed コールバックの戻り値
     * @throws TransactionException
     */
    public function executeInTransaction(callable $callback, string $operation = 'unknown', array $context = []): mixed
    {
        return $this->executeTransactionAction->execute($callback, $operation, $context);
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
    public function executeMultiple(array $callbacks, string $operation = 'multiple', array $context = []): array
    {
        return $this->executeTransactionAction->executeMultiple($callbacks, $operation, $context);
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
        return $this->executeTransactionAction->withManualControl($callback);
    }

    /**
     * トランザクションを開始する
     *
     * @throws TransactionException
     */
    public function beginTransaction(): void
    {
        $this->beginTransaction();
    }

    /**
     * トランザクションをコミットする
     *
     * @throws TransactionException
     */
    public function commit(): void
    {
        $this->commit();
    }

    /**
     * トランザクションをロールバックする
     *
     * @throws TransactionException
     */
    public function rollBack(): void
    {
        $this->rollBack();
    }
}
