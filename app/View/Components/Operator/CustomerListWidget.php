<?php

namespace App\View\Components\Operator;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Customer\CountService as CustomerCountService;
use App\Services\Customer\ListService as CustomerListService;

class CustomerListWidget extends Component
{
    public $users;

    protected $customerListService;
    protected $customerCountService;
    public $customer_count;
    public $new_customer_count;
    public $line_customer_count;

    public function __construct(CustomerListService $customerListService, CustomerCountService $customerCountService, $customers = null)
    {
        $this->customerListService = $customerListService;
        $this->customers = $customers ?? $this->customerListService->getList();

        $this->customerCountService = $customerCountService;
        $this->customer_count = $this->customerCountService->getUserCount();
        $this->new_customer_count = $this->customerCountService->getNewUserCount();
    }

    public function render()
    {
        return view('components.operator.customer-list-widget', [
            'customers' => $this->customers,
            'customer_count' => $this->customer_count,
            'new_customer_count' => $this->new_customer_count,
        ]);
    }
}