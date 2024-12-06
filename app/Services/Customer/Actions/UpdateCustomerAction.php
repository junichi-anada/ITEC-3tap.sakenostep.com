<?php

namespace App\Services\Customer\Actions;

use App\Models\User;
use App\Services\Customer\DTOs\CustomerData;
use App\Services\Customer\Exceptions\CustomerException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UpdateCustomerAction
{
    use OperatorActionTrait;

    /**
     * 顧客情報を更新します
     *
     * @param int $customerId
     * @param CustomerData $data
     * @param int $operatorId
     * @return CustomerData
     * @throws CustomerException
     */
    public function execute(int $customerId, CustomerData $data, int $operatorId): CustomerData
    {
        if (!$this->hasPermission($operatorId)) {
            throw CustomerException::updateFailed($customerId, 'Operator does not have permission');
        }

        $user = User::find($customerId);
        if (!$user) {
            throw CustomerException::notFound($customerId);
        }

        $this->validateData($data, $customerId);

        try {
            DB::beginTransaction();

            $user->name = $data->name;
            $user->email = $data->email;
            $user->phone = $data->phone;
            $user->is_active = $data->isActive;
            if ($data->metadata) {
                $user->metadata = $data->metadata;
            }
            $user->save();

            $this->logOperation($operatorId, 'customer.update', [
                'customer_id' => $user->id,
                'customer_email' => $user->email
            ]);

            DB::commit();

            return CustomerData::fromArray($user->toArray());
        } catch (Throwable $e) {
            DB::rollBack();
            throw CustomerException::updateFailed($customerId, $e->getMessage());
        }
    }

    /**
     * データのバリデーションを行います
     *
     * @param CustomerData $data
     * @param int $customerId
     * @throws CustomerException
     */
    private function validateData(CustomerData $data, int $customerId): void
    {
        $validator = Validator::make($data->toArray(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customerId,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw CustomerException::invalidData($validator->errors()->first());
        }
    }
}
