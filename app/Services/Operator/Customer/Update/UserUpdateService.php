<?php

namespace App\Services\Operator\Customer\Update;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ユーザー情報更新サービスクラス
 *
 * このクラスはユーザー情報を更新するためのサービスを提供します。
 */
final class UserUpdateService
{
    /**
     * ユーザー情報を更新する
     *
     * @param int $userId ユーザーID
     * @param array $data 更新データ
     * @return void
     * @throws \Exception 更新に失敗した場合
     */
    public function updateUser(int $userId, array $data): void
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userId);
            $user->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user: ' . $e->getMessage());
            throw new \Exception('ユーザー情報の更新に失敗しました。');
        }
    }
}
