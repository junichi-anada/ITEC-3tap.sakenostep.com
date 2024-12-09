<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Item;

use App\Services\Item\DTOs\ItemListData;
use App\Services\Item\Queries\GetItemListQuery;
use Illuminate\View\Component;

/**
 * 商品一覧表示コンポーネント
 */
class ItemListComponent extends Component
{
    /**
     * @var ItemListData 商品一覧データ
     */
    public readonly ItemListData $data;

    /**
     * @param GetItemListQuery $query
     */
    public function __construct(
        private readonly GetItemListQuery $query
    ) {
        $searchParams = request()->only([
            'item_code',
            'name',
            'maker_name',
            'category_id',
            'published_at_from',
            'published_at_to',
            'from_source',
            'is_recommended'
        ]);
        $this->data = $this->query->execute($searchParams);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.item.item-list-component', [
            'items' => $this->data->items
        ]);
    }
}
