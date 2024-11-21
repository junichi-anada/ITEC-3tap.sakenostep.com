<?php

namespace App\View\Components\Widget\Operator\Order;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Operator\Item\CountService as ItemCountService;
use App\Services\Operator\Order\Read\Component\List\OrderListService as OrderListService;
use App\Services\Operator\Order\Read\Component\Count\OrderCountService as OrderCountService;

class OrderListComponent extends Component
{
    public $users;

    protected $itemListService;
    protected $itemCountService;
    public $item_count;
    public $new_item_count;
    public $line_item_count;

    public function __construct(OrderListService $orderListService, OrderCountService $orderCountService, $items = null)
    {
        $this->orderListService = $orderListService;
        $this->orders = $orders ?? $this->orderListService->getOrderList();

        $this->orderCountService = $orderCountService;
        $this->order_count = $this->orderCountService->getOrderCount();
        $this->new_order_count = $this->orderCountService->getNewOrderCount();
    }

    public function render()
    {
        return view('components.widget.operator.item.CustomerList', [
            'items' => $this->items,
            'item_count' => $this->item_count,
            'new_item_count' => $this->new_item_count,
        ]);
    }
}
