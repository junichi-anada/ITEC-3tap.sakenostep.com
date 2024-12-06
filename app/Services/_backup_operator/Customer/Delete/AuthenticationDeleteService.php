<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\Authenticate;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * 認証情報削除サービスクラス
 *
 * このクラスは認証情報を削除するためのサービスを提供します。
 */
final class AuthenticationDeleteService
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
     * 認証情報を削除する
     *
     * @param int $authId 認証ID
     * @return void
     * @throws \Exception 削除に失敗した場合
     */
    public function deleteAuthenticate(int $authId): void
    {
        try {
            $this->transactionService->execute(function () use ($authId) {
                $auth = Authenticate::findOrFail($authId);
                $auth->delete();
            });
        } catch (\Exception $e) {
            $this->logService->logError('Failed to delete authentication: ' . $e->getMessage());
            throw new \Exception('認証情報の削除に失敗しました。');
        }
    }
}
