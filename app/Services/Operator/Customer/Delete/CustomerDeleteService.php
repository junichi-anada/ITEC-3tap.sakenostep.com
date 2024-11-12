<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\Authenticate;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;

/**
 * 顧客削除サービスクラス
 *
 * このクラスは顧客情報を削除するためのサービスを提供します。
 */
class CustomerDeleteService
{
    private $userDeleteService;
    private $authenticationDeleteService;
    private $logService;
    private $transactionService;

    public function __construct(
        UserDeleteService $userDeleteService,
        AuthenticationDeleteService $authenticationDeleteService,
        CustomerLogService $logService,
        CustomerTransactionService $transactionService
    ) {
        $this->userDeleteService = $userDeleteService;
        $this->authenticationDeleteService = $authenticationDeleteService;
        $this->logService = $logService;
        $this->transactionService = $transactionService;
    }

    /**
     * 顧客情報を削除する
     *
     * @param string $userCode ユーザーコード
     * @return array 削除結果
     */
    public function deleteCustomer(string $userCode): array
    {
        try {
            return $this->transactionService->execute(function () use ($userCode) {
                $auth = $this->getAuthenticateByUserCode($userCode);

                $this->userDeleteService->deleteUser($auth->entity_id);
                $this->authenticationDeleteService->deleteAuthenticate($auth->id);

                return ['message' => 'success'];
            });
        } catch (\Exception $e) {
            $this->logService->logError('Customer deletion failed: ' . $e->getMessage());
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
