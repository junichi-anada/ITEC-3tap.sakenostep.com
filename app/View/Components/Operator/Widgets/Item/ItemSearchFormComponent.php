<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Item;

use App\Models\ItemCategory;
use Illuminate\View\Component;

/**
 * 商品検索フォームコンポーネント
 */
class ItemSearchFormComponent extends Component
{
    /**
     * @param array $searchParams 検索パラメータ
     */
    public function __construct(
        public readonly array $searchParams = []
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.item.item-search-form-component', [
            'categories' => ItemCategory::all()
        ]);
    }
}
