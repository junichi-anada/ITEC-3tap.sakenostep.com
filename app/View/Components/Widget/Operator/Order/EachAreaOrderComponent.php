<?php

namespace App\View\Components\Widget\Operator\Order;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Operator\Order\Read\Component\Area\AreaOrderService;

class EachAreaOrderComponent extends Component
{
    public $ordersByArea;

    protected $areaOrderService;

    public function __construct(AreaOrderService $areaOrderService)
    {
        $this->areaOrderService = $areaOrderService;
        $this->ordersByArea = $this->areaOrderService->getOrdersByArea();
    }

    public function render()
    {
        return view('components.widget.operator.order.EachAreaOrder');
    }
}
