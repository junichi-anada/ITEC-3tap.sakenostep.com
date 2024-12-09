<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Item;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Illuminate\View\Component;

class ItemDetailComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public readonly Item $item
    ) {
    }

    /**
     * 商品が削除済みかどうかを判定
     *
     * @return bool
     */
    public function isDeleted()
    {
        return !is_null($this->item->deleted_at);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.item.item-detail-component', [
            'categories' => ItemCategory::all(),
            'units' => ItemUnit::all()
        ]);
    }
}
