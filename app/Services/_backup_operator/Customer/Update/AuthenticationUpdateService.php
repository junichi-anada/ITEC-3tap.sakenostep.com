<?php

namespace App\Services\Operator\Customer\Update;

use App\Models\Authenticate;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * 認証情報更新サービスクラス
 *
 * このクラスは認証情報を更新するためのサービスを提供します。
 */
final class AuthenticationUpdateService
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
     * 認証情報を更新する
     *
     * @param int $authId 認証ID
     * @param array $data 更新データ
     * @return void
     * @throws \Exception 更新に失敗した場合
     */
    public function updateAuthenticate(int $authId, array $data): void
    {
        try {
            $this->transactionService->execute(function () use ($authId, $data) {
                $auth = Authenticate::findOrFail($authId);
                $auth->update($data);
            });
        } catch (\Exception $e) {
            $this->logService->logError('Failed to update authentication: ' . $e->getMessage());
            throw new \Exception('認証情報の更新に失敗しました。');
        }
    }
}
