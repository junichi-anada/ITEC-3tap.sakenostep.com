<?php

namespace App\View\Components\Operator\Widgets\Order;

use Illuminate\View\Component;

class OrderSearchFormComponent extends Component
{
    /**
     * コンポーネントを作成
     */
    public function __construct()
    {
    }

    /**
     * コンポーネントを描画
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.order.order-search-form-component');
    }
}
