<?php

namespace App\View\Components\Operator\Widgets\Order;

use Illuminate\View\Component;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderListComponent extends Component
{
    /**
     * @var LengthAwarePaginator
     */
    public $orders;

    /**
     * コンポーネントを作成
     *
     * @param LengthAwarePaginator $orders
     */
    public function __construct(LengthAwarePaginator $orders)
    {
        $this->orders = $orders;
    }

    /**
     * コンポーネントを描画
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.order.order-list-component');
    }
}
