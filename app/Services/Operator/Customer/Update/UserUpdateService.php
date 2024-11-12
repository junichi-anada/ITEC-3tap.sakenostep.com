<?php

namespace App\Services\Operator\Customer\Update;

use App\Models\User;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * ユーザー情報更新サービスクラス
 *
 * このクラスはユーザー情報を更新するためのサービスを提供します。
 */
final class UserUpdateService
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
     * ユーザー情報を更新する
     *
     * @param int $userId ユーザーID
     * @param array $data 更新データ
     * @return void
     * @throws \Exception 更新に失敗した場合
     */
    public function updateUser(int $userId, array $data): void
    {
        try {
            $this->transactionService->execute(function () use ($userId, $data) {
                $user = User::findOrFail($userId);
                $user->update($data);
            });
        } catch (\Exception $e) {
            $this->logService->logError('Failed to update user: ' . $e->getMessage());
            throw new \Exception('ユーザー情報の更新に失敗しました。');
        }
    }
}
