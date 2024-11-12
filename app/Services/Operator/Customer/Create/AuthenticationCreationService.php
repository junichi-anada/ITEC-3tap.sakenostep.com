<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\Authenticate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 認証情報作成サービスクラス
 *
 * このクラスは新しい認証情報を作成するためのサービスを提供します。
 */
final class AuthenticationCreationService
{
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
        DB::beginTransaction();
        try {
            Authenticate::create([
                'auth_code' => Str::uuid(),
                'site_id' => $siteId,
                'entity_type' => User::class,
                'entity_id' => $userId,
                'login_code' => $loginCode,
                'password' => $password,
                'expires_at' => now()->addDays(365),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create authentication: ' . $e->getMessage());
            throw new \Exception('認証情報の作成に失敗しました。');
        }
    }
}
