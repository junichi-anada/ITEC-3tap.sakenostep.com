<?php

namespace App\View\Components\Operator;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Order\PopularItemService;

class PopularItemWidget extends Component
{
    public $popularItems;

    protected $popularItemService;

    public function __construct(PopularItemService $popularItemService)
    {
        $this->popularItemService = $popularItemService;
        $this->popularItems = $this->popularItemService->getPopularItems();
    }

    public function render()
    {
        return view('components.operator.popular-item-widget');
    }
}