<?php

namespace App\View\Components\Operator\Widgets\Item;

use App\Services\Item\Queries\GetPopularItemsQuery;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PopularRankingComponent extends Component
{
    public Collection $popularItems;

    public function __construct(GetPopularItemsQuery $query)
    {
        $this->popularItems = $query->execute(10);
    }

    public function render()
    {
        return view('components.operator.widgets.item.popular-ranking-component');
    }
}
