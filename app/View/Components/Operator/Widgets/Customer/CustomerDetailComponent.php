<?php

namespace App\View\Components\Operator\Widgets\Customer;

use Illuminate\View\Component;

class CustomerDetailComponent extends Component
{
    public $user;
    public $authenticate;
    public $lineUser;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user, $authenticate, $lineUser = null)
    {
        $this->user = $user;
        $this->authenticate = $authenticate;
        $this->lineUser = $lineUser;
    }

    /**
     * ユーザーが削除済みかどうかを判定
     *
     * @return bool
     */
    public function isDeleted()
    {
        return !is_null($this->user->deleted_at);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.customer-detail-component');
    }
}
