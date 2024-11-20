<?php
declare(strict_types=1);

/**
 * トランザクションサービス
 *
 * トランザクション処理を一元管理し、例外処理を統一的に扱います。
 *
 * @category サービス
 * @package App\Services\Transaction
 * @version 1.0
 */

namespace App\Services\Transaction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class TransactionService
{
    /**
     * トランザクション内でコールバックを実行します。
     *
     * @param callable $callback 実行するコールバック関数
     * @return mixed コールバックの戻り値
     * @throws \Exception 処理中に発生した例外
     */
    public function executeInTransaction(callable $callback)
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('トランザクション処理でエラーが発生しました。', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('データベース処理中にエラーが発生しました: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * トランザクションを開始します。
     *
     * @return void
     * @throws \Exception トランザクション開始に失敗した場合
     */
    public function beginTransaction(): void
    {
        try {
            DB::beginTransaction();
        } catch (Throwable $e) {
            Log::error('トランザクション開始に失敗しました。', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('トランザクション開始に失敗しました: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * トランザクションをコミットします。
     *
     * @return void
     * @throws \Exception コミットに失敗した場合
     */
    public function commit(): void
    {
        try {
            DB::commit();
        } catch (Throwable $e) {
            Log::error('トランザクションのコミットに失敗しました。', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('トランザクションのコミットに失敗しました: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * トランザクションをロールバックします。
     *
     * @return void
     * @throws \Exception ロールバックに失敗した場合
     */
    public function rollBack(): void
    {
        try {
            DB::rollBack();
        } catch (Throwable $e) {
            Log::error('トランザクションのロールバックに失敗しました。', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('トランザクションのロールバックに失敗しました: ' . $e->getMessage(), 0, $e);
        }
    }
}
