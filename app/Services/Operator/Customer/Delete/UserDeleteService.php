<?php

namespace App\Services\Operator\Customer\Delete;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ユーザー削除サービスクラス
 *
 * このクラスはユーザー情報を削除するためのサービスを提供します。
 */
final class UserDeleteService
{
    /**
     * ユーザーを削除する
     *
     * @param int $userId ユーザーID
     * @return void
     * @throws \Exception 削除に失敗した場合
     */
    public function deleteUser(int $userId): void
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($userId);
            $user->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user: ' . $e->getMessage());
            throw new \Exception('ユーザーの削除に失敗しました。');
        }
    }
}
