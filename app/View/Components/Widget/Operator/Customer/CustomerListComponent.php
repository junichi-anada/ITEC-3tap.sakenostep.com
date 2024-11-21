<?php

namespace App\View\Components\Widget\Operator\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Operator\Customer\Read\Component\Count\CountService as CountService;
use App\Services\Operator\Customer\Read\Component\List\CustomerListService as CustomerListService;

class CustomerListComponent extends Component
{
    public $users;

    protected $customerListService;
    protected $UserCountService;
    public $customer_count;
    public $new_customer_count;
    public $line_customer_count;

    public function __construct(CustomerListService $customerListService, CountService $countService, $customers = null)
    {
        $this->customerListService = $customerListService;
        $this->customers = $customers ?? $this->customerListService->getList();

        $this->countService = $countService;
        $this->customer_count = $this->countService->getUserCount();
        $this->new_customer_count = $this->countService->getNewUserCount();
        $this->line_customer_count = $this->countService->getLineUserCount();
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
