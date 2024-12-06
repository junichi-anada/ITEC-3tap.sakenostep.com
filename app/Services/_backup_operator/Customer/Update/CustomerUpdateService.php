<?php

namespace App\Services\Operator\Customer\Update;

use App\Models\Authenticate;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * 顧客情報更新サービスクラス
 *
 * このクラスは顧客情報を更新するためのサービスを提供します。
 */
class CustomerUpdateService
{
    private $userUpdateService;
    private $authenticationUpdateService;
    private $logService;
    private $transactionService;

    public function __construct(
        UserUpdateService $userUpdateService,
        AuthenticationUpdateService $authenticationUpdateService,
        CustomerLogService $logService,
        CustomerTransactionService $transactionService
    ) {
        $this->userUpdateService = $userUpdateService;
        $this->authenticationUpdateService = $authenticationUpdateService;
        $this->logService = $logService;
        $this->transactionService = $transactionService;
    }

    /**
     * 顧客情報を更新する
     *
     * @param string $userCode ユーザーコード
     * @param array $data 更新データ
     * @return array 更新結果
     */
    public function updateCustomer(string $userCode, array $data): array
    {
        try {
            return $this->transactionService->execute(function () use ($userCode, $data) {
                $auth = $this->getAuthenticateByUserCode($userCode);

                $this->userUpdateService->updateUser($auth->entity_id, $data);
                $this->authenticationUpdateService->updateAuthenticate($auth->id, $data);

                return ['message' => 'success'];
            });
        } catch (\Exception $e) {
            $this->logService->logError('Customer update failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }

    /**
     * ユーザーコードから認証情報を取得する
     *
     * @param string $userCode ユーザーコード
     * @return Authenticate 認証情報
     * @throws \Exception 認証情報が見つからない場合
     */
    private function getAuthenticateByUserCode(string $userCode): Authenticate
    {
        $auth = Authenticate::where('login_code', $userCode)->first();

        if (!$auth) {
            throw new \Exception('認証情報が見つかりません。');
        }

        return $auth;
    }
}
