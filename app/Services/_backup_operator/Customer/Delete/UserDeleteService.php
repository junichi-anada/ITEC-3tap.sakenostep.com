<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\User;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * ユーザー削除サービスクラス
 *
 * このクラスはユーザー情報を削除するためのサービスを提供します。
 */
final class UserDeleteService
{
    private CustomerLogService $logService;
    private CustomerTransactionService $transactionService;

    public function __construct(
        CustomerLogService $logService,
        CustomerTransactionService $transactionService
    ) {
        $this->logService = $logService;
        $this->transactionService = $transactionService;
    }

    /**
     * ユーザーを削除する
     *
     * @param int $userId ユーザーID
     * @return void
     * @throws \Exception 削除に失敗した場合
     */
    public function deleteUser(int $userId): void
    {
        try {
            $this->transactionService->execute(function () use ($userId) {
                $user = User::findOrFail($userId);
                $user->delete();
            });
        } catch (\Exception $e) {
            $this->logService->logError('Failed to delete user: ' . $e->getMessage());
            throw new \Exception('ユーザーの削除に失敗しました。');
        }
    }
}
