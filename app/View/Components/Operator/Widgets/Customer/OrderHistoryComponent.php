<?php

namespace App\View\Components\Operator\Widgets\Customer;

use Illuminate\View\Component;
use App\Models\User;
use App\Models\Order;

class OrderHistoryComponent extends Component
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $orders;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.order-history-component');
    }
}
