<?php

namespace App\Services\Operator\Item\Delete;

use App\Models\Authenticate;
use App\Models\User;
use App\Services\Operator\Item\Log\ItemLogService;
use App\Services\Operator\Item\Transaction\ItemTransactionService;

/**
 * アイテム削除サービスクラス
 *
 * このクラスはアイテムを削除するためのサービスを提供します。
 */
class DeleteService
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

    public function deleteCustomer($userCode)
    {
        try {
            return $this->transactionService->execute(function () use ($userCode) {
                // 認証テーブルから該当のログインIDのsite_idとentity_idを取得
                $auth = Authenticate::where('login_code', $userCode)->first();

                if (!$auth) {
                    throw new \Exception('認証情報が見つかりません。');
                }

                // 認証情報を削除
                Authenticate::where('login_code', $userCode)->delete();

                // ユーザーをsite_idとidで削除
                User::where('site_id', $auth->site_id)->where('id', $auth->entity_id)->delete();

                return ['message' => 'success'];
            });
        } catch (\Exception $e) {
            $this->logService->logError('Customer deletion failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}
