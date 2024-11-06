<?php

namespace App\View\Components\Operator;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Order\AreaOrderService;

class AreaOrderWidget extends Component
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
        return view('components.operator.area-order-widget');
    }
}