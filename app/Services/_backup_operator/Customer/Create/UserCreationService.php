<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\User;
use App\Services\Operator\Customer\Log\CustomerLogService;
use App\Services\Operator\Customer\Transaction\CustomerTransactionService;
use Illuminate\Http\Request;

/**
 * ユーザー作成サービスクラス
 *
 * このクラスは新しいユーザーを作成するためのサービスを提供します。
 */
final class UserCreationService
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
     * ユーザーを作成する
     *
     * @param array $userData ユーザーデータの配列
     * @param int $siteId サイトID
     * @return User 作成されたユーザー
     * @throws \Exception 作成に失敗した場合
     */
    public function createUser(array $userData, int $siteId): User
    {
        try {
            return $this->transactionService->execute(function () use ($userData, $siteId) {
                return User::create([
                    'user_code' => $userData['user_code'],
                    'name' => $userData['name'],
                    'site_id' => $siteId,
                    'phone' => $userData['phone'],
                    'phone2' => $userData['phone2'] ?? null,
                    'fax' => $userData['fax'] ?? null,
                    'postal_code' => $userData['postal_code'],
                    'address' => $userData['address'],
                ]);
            });
        } catch (\Exception $e) {
            $this->logService->logError('Failed to create user: ' . $e->getMessage());
            throw new \Exception('ユーザーの作成に失敗しました。');
        }
    }
}
