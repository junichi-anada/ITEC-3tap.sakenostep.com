<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\Authenticate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 顧客削除サービスクラス
 *
 * このクラスは顧客情報を削除するためのサービスを提供します。
 */
class CustomerDeleteService
{
    private $userDeleteService;
    private $authenticationDeleteService;

    public function __construct(
        UserDeleteService $userDeleteService,
        AuthenticationDeleteService $authenticationDeleteService
    ) {
        $this->userDeleteService = $userDeleteService;
        $this->authenticationDeleteService = $authenticationDeleteService;
    }

    /**
     * 顧客情報を削除する
     *
     * @param string $userCode ユーザーコード
     * @return array 削除結果
     */
    public function deleteCustomer(string $userCode): array
    {
        DB::beginTransaction();
        try {
            $auth = $this->getAuthenticateByUserCode($userCode);

            $this->userDeleteService->deleteUser($auth->entity_id);
            $this->authenticationDeleteService->deleteAuthenticate($auth->id);

            DB::commit();
            return ['message' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer deletion failed: ' . $e->getMessage());
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
