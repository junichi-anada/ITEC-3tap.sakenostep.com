<?php

namespace App\View\Components\Widget\Operator\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RegistComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct($customerCode)
    {
        $this->customerCode = $customerCode;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widget.operator.customer.Regist', [
            'customer_code' => $this->customerCode,
        ]);
    }
}
