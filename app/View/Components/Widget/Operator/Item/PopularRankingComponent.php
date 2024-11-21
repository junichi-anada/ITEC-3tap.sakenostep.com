<?php

namespace App\View\Components\Widget\Operator\Item;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Order\Component\PopularItemService;

class PopularRankingComponent extends Component
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
        return view('components.widget.operator.order.PopularRanking');
    }
}
