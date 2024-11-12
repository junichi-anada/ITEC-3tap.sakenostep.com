<?php

namespace App\Services\Operator\Customer\Create;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ユーザー作成サービスクラス
 *
 * このクラスは新しいユーザーを作成するためのサービスを提供します。
 */
final class UserCreationService
{
    /**
     * ユーザーを作成する
     *
     * @param Request $request リクエストオブジェクト
     * @param int $siteId サイトID
     * @return User 作成されたユーザー
     * @throws \Exception 作成に失敗した場合
     */
    public function createUser(Request $request, int $siteId): User
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'user_code' => $request->user_code,
                'site_id' => $siteId,
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user: ' . $e->getMessage());
            throw new \Exception('ユーザーの作成に失敗しました。');
        }
    }
}
