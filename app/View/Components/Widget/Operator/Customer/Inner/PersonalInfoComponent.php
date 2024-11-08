<?php

namespace App\View\Components\Widget\Operator\Customer\Inner;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PersonalInfoComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widget.operator.customer.inner.PersonalInfo', [
            'customer' => $this->customer,
        ]);
    }
}
