<?php

namespace App\Services\Customer;

use App\Services\Customer\Actions\CreateCustomerAction;
use App\Services\Customer\Actions\UpdateCustomerAction;
use App\Services\Customer\Actions\DeleteCustomerAction;
use App\Services\Customer\Actions\ImportCustomerAction;
use App\Services\Customer\DTOs\CustomerData;
use App\Services\Customer\DTOs\CustomerSearchCriteria;
use App\Services\Customer\Exceptions\CustomerException;
use App\Services\Customer\Queries\SearchCustomerQuery;
use App\Services\Interfaces\OperatorServiceInterface;
use App\Services\ServiceErrorHandler;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService implements OperatorServiceInterface
{
    use OperatorActionTrait, ServiceErrorHandler;

    public function __construct(
        private readonly CreateCustomerAction $createCustomerAction,
        private readonly UpdateCustomerAction $updateCustomerAction,
        private readonly DeleteCustomerAction $deleteCustomerAction,
        private readonly ImportCustomerAction $importCustomerAction,
        private readonly SearchCustomerQuery $searchCustomerQuery
    ) {}

    /**
     * 顧客を作成します
     *
     * @param CustomerData $data
     * @param int $operatorId
     * @return CustomerData|null
     */
    public function createCustomer(CustomerData $data, int $operatorId): ?CustomerData
    {
        return $this->tryCatchWrapper(
            callback: fn() => $this->createCustomerAction->execute($data, $operatorId),
            errorMessage: 'Failed to create customer',
            context: ['operator_id' => $operatorId]
        );
    }

    /**
     * 顧客情報を更新します
     *
     * @param int $customerId
     * @param CustomerData $data
     * @param int $operatorId
     * @return CustomerData|null
     */
    public function updateCustomer(int $customerId, CustomerData $data, int $operatorId): ?CustomerData
    {
        return $this->tryCatchWrapper(
            callback: fn() => $this->updateCustomerAction->execute($customerId, $data, $operatorId),
            errorMessage: 'Failed to update customer',
            context: ['customer_id' => $customerId, 'operator_id' => $operatorId]
        );
    }

    /**
     * 顧客を削除します
     *
     * @param int $customerId
     * @param int $operatorId
     * @return bool
     */
    public function deleteCustomer(int $customerId, int $operatorId): bool
    {
        return $this->tryCatchWrapper(
            callback: fn() => $this->deleteCustomerAction->execute($customerId, $operatorId),
            errorMessage: 'Failed to delete customer',
            context: ['customer_id' => $customerId, 'operator_id' => $operatorId]
        ) ?? false;
    }

    /**
     * 顧客データをインポートします
     *
     * @param array $data
     * @param int $operatorId
     * @return array|null
     */
    public function importCustomers(array $data, int $operatorId): ?array
    {
        return $this->tryCatchWrapper(
            callback: fn() => $this->importCustomerAction->execute($data, $operatorId),
            errorMessage: 'Failed to import customers',
            context: ['operator_id' => $operatorId]
        );
    }

    /**
     * 顧客を検索します
     *
     * @param CustomerSearchCriteria $criteria
     * @return LengthAwarePaginator|null
     */
    public function searchCustomers(CustomerSearchCriteria $criteria): ?LengthAwarePaginator
    {
        return $this->tryCatchWrapper(
            callback: fn() => $this->searchCustomerQuery->execute($criteria),
            errorMessage: 'Failed to search customers'
        );
    }

    /**
     * 検索条件に基づいて顧客数を取得します
     *
     * @param CustomerSearchCriteria $criteria
     * @return int
     */
    public function countCustomers(CustomerSearchCriteria $criteria): int
    {
        return $this->tryCatchWrapper(
            callback: fn() => $this->searchCustomerQuery->count($criteria),
            errorMessage: 'Failed to count customers'
        ) ?? 0;
    }
}
