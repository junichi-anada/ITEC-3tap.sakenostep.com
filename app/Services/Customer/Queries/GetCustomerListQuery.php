<?php

declare(strict_types=1);

namespace App\Services\Customer\Queries;

use App\Models\User;
use App\Models\Authenticate;
use App\Services\Customer\DTOs\CustomerListData;
use Illuminate\Database\Eloquent\Builder;

class GetCustomerListQuery
{
    /**
     * 顧客一覧を取得する
     *
     * @param array $searchParams 検索条件
     * @param int $perPage 1ページあたりの表示件数
     * @return CustomerListData
     */
    public function execute(array $searchParams = [], int $perPage = 10): CustomerListData
    {
        $query = User::query()
            ->join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->select(
                'users.*',
                'authenticates.login_code',
                'authenticates.created_at as first_login_at',
                'authenticates.updated_at as last_login_at'
            )
            ->where('authenticates.entity_type', User::class)
            ->whereNull('users.deleted_at');

        // 検索条件の適用
        if (!empty($searchParams['customer_code'])) {
            $query->where('authenticates.login_code', 'like', '%' . $searchParams['customer_code'] . '%');
        }

        if (!empty($searchParams['customer_name'])) {
            $query->where('users.name', 'like', '%' . $searchParams['customer_name'] . '%');
        }

        if (!empty($searchParams['customer_address'])) {
            $query->where('users.address', 'like', '%' . $searchParams['customer_address'] . '%');
        }

        if (!empty($searchParams['customer_phone'])) {
            $query->where('users.phone', 'like', '%' . $searchParams['customer_phone'] . '%');
        }

        if (!empty($searchParams['first_login_date_from'])) {
            $query->where('authenticates.created_at', '>=', $searchParams['first_login_date_from']);
        }

        if (!empty($searchParams['first_login_date_to'])) {
            $query->where('authenticates.created_at', '<=', $searchParams['first_login_date_to'] . ' 23:59:59');
        }

        if (!empty($searchParams['last_login_date_from'])) {
            $query->where('authenticates.updated_at', '>=', $searchParams['last_login_date_from']);
        }

        if (!empty($searchParams['last_login_date_to'])) {
            $query->where('authenticates.updated_at', '<=', $searchParams['last_login_date_to'] . ' 23:59:59');
        }

        $customers = $query->paginate($perPage);

        return new CustomerListData($customers);
    }
}
