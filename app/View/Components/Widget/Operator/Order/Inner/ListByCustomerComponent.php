<?php

namespace App\View\Components\Widget\Operator\Order\inner;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListByCustomerComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct($orders, $customer)
    {
        $this->orders = $orders;
        $this->customer = $customer;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widget.operator.order.inner.ListByCustomer', [
            'orders' => $this->orders,
            'customer' => $this->customer,
        ]);
    }
}
