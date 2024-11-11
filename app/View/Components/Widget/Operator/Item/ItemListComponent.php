<?php

namespace App\View\Components\Widget\Operator\Item;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\Operator\Item\CountService as ItemCountService;
use App\Services\Operator\Item\ListService as ItemListService;

class ItemListComponent extends Component
{
    public $users;

    protected $itemListService;
    protected $itemCountService;
    public $item_count;
    public $new_item_count;
    public $line_item_count;

    public function __construct(ItemListService $itemListService, ItemCountService $itemCountService, $items = null)
    {
        $this->itemListService = $itemListService;
        $this->items = $items ?? $this->itemListService->getList();

        $this->itemCountService = $itemCountService;
        $this->item_count = $this->itemCountService->getUserCount();
        $this->new_item_count = $this->itemCountService->getNewUserCount();
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