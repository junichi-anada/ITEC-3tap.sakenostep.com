<?php

namespace App\Services\Operator\Customer;

use App\Models\User;
use App\Services\Auth\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerUpdateService
{
    /**
     * 顧客情報を更新する
     *
     * @param int $userId ユーザーID
     * @param array $data 更新データ
     * @return User
     * @throws AuthenticationException
     */
    public function update(int $userId, array $data): User
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            
            // 電話番号が変更されているかチェック
            $phoneNumberChanged = isset($data['phone_number']) && 
                                $data['phone_number'] !== $user->phone_number;

            // ユーザー情報を更新
            $user->fill($data);
            
            // 電話番号が変更された場合、パスワードも更新
            if ($phoneNumberChanged) {
                $user->password = $data['phone_number'];
                Log::info('顧客パスワード更新', [
                    'user_id' => $user->id,
                    'phone_number' => $data['phone_number']
                ]);
            }

            if (!$user->save()) {
                throw AuthenticationException::passwordUpdateFailed();
            }

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('顧客情報更新エラー: ' . $e->getMessage(), [
                'user_id' => $userId,
                'data' => $data
            ]);
            throw $e;
        }
    }
} 