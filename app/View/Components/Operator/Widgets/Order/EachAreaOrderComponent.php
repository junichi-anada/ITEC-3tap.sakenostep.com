<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Order;

use App\Services\Order\Analytics\AreaOrderAnalytics;
use Illuminate\View\Component;

class EachAreaOrderComponent extends Component
{
    /**
     * @var array<string, int>
     */
    public array $areaOrders;

    public function __construct(
        private readonly AreaOrderAnalytics $analytics
    ) {
        $this->areaOrders = $this->analytics->getOrdersByArea();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.order.each-area-order-component');
    }
}
