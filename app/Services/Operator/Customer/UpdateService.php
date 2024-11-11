<?php

namespace App\Services\Operator\Customer;

use App\Models\Authenticate;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UpdateService
{
    public function updateCustomer($userCode, $data)
    {
        try {
            // 認証テーブルから該当のログインIDのsite_idとentity_idを取得
            $auth = Authenticate::where('login_code', $userCode)->first();

            if (!$auth) {
                throw new \Exception('認証情報が見つかりません。');
            }

            // ユーザー情報を更新
            $user = User::findOrFail($auth->entity_id);
            $user->update($data);

            return ['message' => 'success'];
        } catch (\Exception $e) {
            Log::error('Customer update failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}