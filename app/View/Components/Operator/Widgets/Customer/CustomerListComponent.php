<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Customer;

use App\Services\Customer\DTOs\CustomerListData;
use App\Services\Customer\Queries\GetCustomerListQuery;
use Illuminate\View\Component;

/**
 * 顧客一覧表示コンポーネント
 */
class CustomerListComponent extends Component
{
    /**
     * @var CustomerListData 顧客一覧データ
     */
    public readonly CustomerListData $data;

    /**
     * @param GetCustomerListQuery $query
     */
    public function __construct(
        private readonly GetCustomerListQuery $query
    ) {
        $searchParams = request()->only([
            'customer_code',
            'customer_name',
            'customer_address',
            'customer_phone',
            'first_login_date_from',
            'first_login_date_to',
            'last_login_date_from',
            'last_login_date_to'
        ]);
        $this->data = $this->query->execute($searchParams);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.customer-list-component', [
            'customers' => $this->data->customers
        ]);
    }
}
