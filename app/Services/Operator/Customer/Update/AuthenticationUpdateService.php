<?php

namespace App\Services\Operator\Customer\Update;

use App\Models\Authenticate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 認証情報更新サービスクラス
 *
 * このクラスは認証情報を更新するためのサービスを提供します。
 */
final class AuthenticationUpdateService
{
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
        DB::beginTransaction();
        try {
            $auth = Authenticate::findOrFail($authId);
            $auth->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update authentication: ' . $e->getMessage());
            throw new \Exception('認証情報の更新に失敗しました。');
        }
    }
}
