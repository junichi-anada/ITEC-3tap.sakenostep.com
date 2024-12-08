<?php

namespace App\Services\Customer\Queries;

use App\Models\User;
use App\Models\Authenticate;
use App\Services\Customer\DTOs\CustomerListData;

class CustomerSearchQuery
{
    public function execute($search)
    {
        $query = User::query()
            ->join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->where('authenticates.entity_type', User::class)
            ->whereNull('users.deleted_at');

        // Apply search filters
        if (!empty($search['customer_code'])) {
            $query->where('authenticates.login_code', 'like', '%' . $search['customer_code'] . '%');
        }
        if (!empty($search['customer_name'])) {
            $query->where('users.name', 'like', '%' . $search['customer_name'] . '%');
        }
        if (!empty($search['customer_address'])) {
            $query->where('users.address', 'like', '%' . $search['customer_address'] . '%');
        }
        if (!empty($search['customer_phone'])) {
            $query->where('users.phone', 'like', '%' . $search['customer_phone'] . '%');
        }
        if (!empty($search['first_login_date_from'])) {
            $query->where('authenticates.created_at', '>=', $search['first_login_date_from']);
        }
        if (!empty($search['first_login_date_to'])) {
            $query->where('authenticates.created_at', '<=', $search['first_login_date_to']);
        }
        if (!empty($search['last_login_date_from'])) {
            $query->where('authenticates.updated_at', '>=', $search['last_login_date_from']);
        }
        if (!empty($search['last_login_date_to'])) {
            $query->where('authenticates.updated_at', '<=', $search['last_login_date_to']);
        }

        $customers = $query->paginate(10);

        return new CustomerListData($customers);
    }
}
