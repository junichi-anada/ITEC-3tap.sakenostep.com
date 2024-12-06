<?php

namespace App\Services\Operator\Item;

use App\Models\Authenticate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteService
{
    public function deleteCustomer($userCode)
    {
        DB::beginTransaction();

        try {
            // 認証テーブルから該当のログインIDのsite_idとentity_idを取得
            $auth = Authenticate::where('login_code', $userCode)->first();

            if (!$auth) {
                throw new \Exception('認証情報が見つかりません。');
            }

            // 認証情報を削除
            Authenticate::where('login_code', $userCode)->delete();

            // ユーザーをsite_idとidで削除
            User::where('site_id', $auth->site_id)->where('id', $auth->entity_id)->delete();

            DB::commit();

            return ['message' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer deletion failed: ' . $e->getMessage());
            return ['message' => 'fail', 'reason' => $e->getMessage()];
        }
    }
}