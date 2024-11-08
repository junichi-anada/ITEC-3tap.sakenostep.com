<?php

namespace App\View\Components\Widget\Operator\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Customer\CountService as CustomerCountService;
use App\Services\Customer\ListService as CustomerListService;

class CustomerListComponent extends Component
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
        return view('components.widget.operator.customer.CustomerList', [
            'customers' => $this->customers,
            'customer_count' => $this->customer_count,
            'new_customer_count' => $this->new_customer_count,
        ]);
    }
}