<?php

namespace App\Services\Order\Operator;

use App\Models\OrderDetail;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class PopularItemService
{
    /**
     * 人気商品を取得
     *
     * @return Item[]
     */
    public function getPopularItems()
    {
      $popularItems = OrderDetail::select('item_id', DB::raw('sum(volume) as sum_volume'))
          ->groupBy('item_id')
          ->orderBy('sum_volume', 'desc')
          ->limit(5)
          ->get();

      $itemIds = $popularItems->pluck('item_id')->toArray();

      $items = Item::whereIn('id', $itemIds)->get();

      return $items;
    }

}