<?php

namespace App\Services\Customer\Actions;

use App\Models\User;
use App\Services\Customer\DTOs\CustomerData;
use App\Services\Customer\Exceptions\CustomerException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ImportCustomerAction
{
    use OperatorActionTrait;

    /**
     * 顧客データをインポートします
     *
     * @param array $data インポートするデータの配列
     * @param int $operatorId
     * @return array 処理結果の配列 ['success' => int, 'failed' => int, 'errors' => array]
     * @throws CustomerException
     */
    public function execute(array $data, int $operatorId): array
    {
        if (!$this->hasPermission($operatorId)) {
            throw CustomerException::importFailed('Operator does not have permission');
        }

        $result = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                try {
                    $this->validateImportRow($row);
                    
                    $customerData = new CustomerData(
                        id: null,
                        name: $row['name'],
                        email: $row['email'],
                        phone: $row['phone'] ?? null,
                        isActive: $row['is_active'] ?? true,
                        metadata: $row['metadata'] ?? []
                    );

                    // メールアドレスが既存の場合はスキップ
                    if (User::where('email', $customerData->email)->exists()) {
                        $result['failed']++;
                        $result['errors'][] = [
                            'row' => $index + 1,
                            'message' => "Email {$customerData->email} already exists"
                        ];
                        continue;
                    }

                    $user = new User();
                    $user->name = $customerData->name;
                    $user->email = $customerData->email;
                    $user->phone = $customerData->phone;
                    $user->is_active = $customerData->isActive;
                    $user->metadata = $customerData->metadata;
                    $user->save();

                    $result['success']++;

                } catch (Throwable $e) {
                    $result['failed']++;
                    $result['errors'][] = [
                        'row' => $index + 1,
                        'message' => $e->getMessage()
                    ];
                }
            }

            $this->logOperation($operatorId, 'customer.import', [
                'total' => count($data),
                'success' => $result['success'],
                'failed' => $result['failed']
            ]);

            DB::commit();
            return $result;

        } catch (Throwable $e) {
            DB::rollBack();
            throw CustomerException::importFailed($e->getMessage());
        }
    }

    /**
     * インポートする行データのバリデーションを行います
     *
     * @param array $row
     * @throws CustomerException
     */
    private function validateImportRow(array $row): void
    {
        $validator = Validator::make($row, [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw CustomerException::invalidData($validator->errors()->first());
        }
    }
}
