<?php

namespace App\Services\Customer\Actions;

use App\Models\User;
use App\Services\Customer\DTOs\CustomerData;
use App\Services\Customer\Exceptions\CustomerException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CreateCustomerAction
{
    use OperatorActionTrait;

    /**
     * 顧客を作成します
     *
     * @param CustomerData $data
     * @param int $operatorId
     * @return CustomerData
     * @throws CustomerException
     */
    public function execute(CustomerData $data, int $operatorId): CustomerData
    {
        if (!$this->hasPermission($operatorId)) {
            throw CustomerException::createFailed('Operator does not have permission');
        }

        $this->validateData($data);

        try {
            DB::beginTransaction();

            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;
            $user->phone = $data->phone;
            $user->is_active = $data->isActive;
            $user->metadata = $data->metadata;
            $user->save();

            $this->logOperation($operatorId, 'customer.create', [
                'customer_id' => $user->id,
                'customer_email' => $user->email
            ]);

            DB::commit();

            return CustomerData::fromArray($user->toArray());
        } catch (Throwable $e) {
            DB::rollBack();
            throw CustomerException::createFailed($e->getMessage());
        }
    }

    /**
     * データのバリデーションを行います
     *
     * @param CustomerData $data
     * @throws CustomerException
     */
    private function validateData(CustomerData $data): void
    {
        $validator = Validator::make($data->toArray(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw CustomerException::invalidData($validator->errors()->first());
        }
    }
}
