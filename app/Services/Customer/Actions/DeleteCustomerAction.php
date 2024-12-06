<?php

namespace App\Services\Customer\Actions;

use App\Models\User;
use App\Services\Customer\Exceptions\CustomerException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteCustomerAction
{
    use OperatorActionTrait;

    /**
     * 顧客を削除します
     *
     * @param int $customerId
     * @param int $operatorId
     * @return bool
     * @throws CustomerException
     */
    public function execute(int $customerId, int $operatorId): bool
    {
        if (!$this->hasPermission($operatorId)) {
            throw CustomerException::deleteFailed($customerId, 'Operator does not have permission');
        }

        $user = User::find($customerId);
        if (!$user) {
            throw CustomerException::notFound($customerId);
        }

        try {
            DB::beginTransaction();

            // 関連データの削除や非アクティブ化などの処理をここに追加
            $user->is_active = false;
            $user->save();

            $this->logOperation($operatorId, 'customer.delete', [
                'customer_id' => $user->id,
                'customer_email' => $user->email
            ]);

            DB::commit();

            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw CustomerException::deleteFailed($customerId, $e->getMessage());
        }
    }
}
