<?php

namespace App\View\Components\Operator\Widgets\Customer;

use Illuminate\View\Component;

class CustomerEditFormComponent extends Component
{
    public $user;
    public $authenticate;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user, $authenticate)
    {
        $this->user = $user;
        $this->authenticate = $authenticate;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.customer-edit-form-component');
    }
}
