<?php

namespace App\View\Components\Widget\Operator\Order;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Operator\Order\Read\Component\List\OrderListService;
use App\Services\Operator\Order\Read\Component\Count\OrderCountService;

class OrderListComponent extends Component
{
    public $orders;
    public $order_count;

    public function __construct(OrderListService $orderListService, OrderCountService $orderCountService, $orders = null)
    {
        $this->orderListService = $orderListService;
        $this->orders = $orders ?? $this->orderListService->getOrderList();

        $this->orderCountService = $orderCountService;
        $this->order_count = $this->orderCountService->getOrderCount(['status' => 'all']);
    }

    public function render()
    {
        return view('components.widget.operator.order.list', [
            'orders' => $this->orders,
            'order_count' => $this->order_count,
        ]);
    }
}
