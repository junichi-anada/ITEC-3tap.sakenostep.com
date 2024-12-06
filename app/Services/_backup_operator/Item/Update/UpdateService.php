<?php

namespace App\Services\Operator\Item\Update;

use App\Models\Authenticate;
use App\Models\User;
use App\Services\Operator\Item\Log\ItemLogService;
use App\Services\Operator\Item\Transaction\ItemTransactionService;

/**
 * アイテム更新サービスクラス
 *
 * このクラスはアイテム情報を更新するためのサービスを提供します。
 */
class UpdateService
{
    private ItemLogService $logService;
    private ItemTransactionService $transactionService;

    public function __construct(
        ItemLogService $logService,
        ItemTransactionService $transactionService
    ) {
        $this->logService = $logService;
        $this->transactionService = $transactionService;
    }

    public function updateCustomer($userCode, $data)
    {
        try {
            return $this->transactionService->execute(function () use ($userCode, $data) {
                // 認証テーブルから該当のログインIDのsite_idとentity_idを取得
                $auth = Authenticate::where('login_code', $userCode)->first();

                if (!$auth) {
                    throw new \Exception('認証情報が見つかりません。');
                }

                // ユーザー情報を更新
                $user = User::findOrFail($auth->entity_id);
                $user->update($data);

                return ['message' => 'success'];
            });
        } catch (\Exception $e) {
            $this->logService->logError('Customer update failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}
