<?php

namespace App\View\Components\Operator\Widgets\Order;

use App\Models\Order;
use Illuminate\View\Component;

class OrderDetailComponent extends Component
{
    /**
     * @var Order
     */
    public $order;

    /**
     * コンポーネントを作成
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * コンポーネントを描画
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.order.order-detail-component');
    }
}
