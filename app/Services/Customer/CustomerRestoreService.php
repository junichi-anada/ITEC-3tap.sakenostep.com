<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Authenticate;
use App\Models\LineUser;
use Illuminate\Support\Facades\DB;

class CustomerRestoreService
{
    public function restoreCustomer(User $user, $auth)
    {
        try {
            DB::beginTransaction();

            // LINE連携情報は新規作成のため、削除は維持

            // 認証情報を復元
            Authenticate::withTrashed()
                ->where('entity_id', $user->id)
                ->where('entity_type', User::class)
                ->get()
                ->each(function ($auth) {
                    $auth->restore(); // deleteの代わりにrestoreを使用
                });

            // ユーザー情報を復元
            $user->restore();

            DB::commit();

            return [
                'message' => 'success'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'error',
                'reason' => $e->getMessage()
            ];
        }
    }
}
