<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\Authenticate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 認証情報削除サービスクラス
 *
 * このクラスは認証情報を削除するためのサービスを提供します。
 */
final class AuthenticationDeleteService
{
    /**
     * 認証情報を削除する
     *
     * @param int $authId 認証ID
     * @return void
     * @throws \Exception 削除に失敗した場合
     */
    public function deleteAuthenticate(int $authId): void
    {
        DB::beginTransaction();
        try {
            $auth = Authenticate::findOrFail($authId);
            $auth->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete authentication: ' . $e->getMessage());
            throw new \Exception('認証情報の削除に失敗しました。');
        }
    }
}
