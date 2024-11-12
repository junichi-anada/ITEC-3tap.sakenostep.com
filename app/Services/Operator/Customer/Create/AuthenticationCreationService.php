<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\Authenticate;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 認証情報作成サービスクラス
 *
 * このクラスは新しい認証情報を作成するためのサービスを提供します。
 */
final class AuthenticationCreationService
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
     * 認証情報を作成する
     *
     * @param int $siteId サイトID
     * @param int $userId ユーザーID
     * @param string $loginCode ログインコード
     * @param string $password パスワード
     * @return void
     * @throws \Exception 作成に失敗した場合
     */
    public function createAuthenticate(int $siteId, int $userId, string $loginCode, string $password): void
    {
        try {
            $this->transactionService->execute(function () use ($siteId, $userId, $loginCode, $password) {
                Authenticate::create([
                    'auth_code' => Str::uuid(),
                    'site_id' => $siteId,
                    'entity_type' => User::class,
                    'entity_id' => $userId,
                    'login_code' => $loginCode,
                    'password' => $password,
                    'expires_at' => now()->addDays(365),
                ]);
            });
        } catch (\Exception $e) {
            $this->logService->logError('Failed to create authentication: ' . $e->getMessage());
            throw new \Exception('認証情報の作成に失敗しました。');
        }
    }
}
