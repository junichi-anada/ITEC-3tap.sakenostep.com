<?php

namespace App\Services\Operator\Item;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ListService
{
    /**
     * 商品一覧を取得
     *
     * @return void
     */
    public function getList()
    {
        $auth = Auth::user();

        $items = Item::where('site_id', $auth->site_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return $items;
    }

    /**
     * 検索条件を受けて商品一覧を取得
     *
     * @return void
     */
    public function searchList($search)
    {
        $auth = Auth::user();

        $items = Item::where('site_id', $auth->site_id);

        if (!empty($search['item_code'])) {
            $items = $items->where('item_code', 'like', '%' . $search['item_code'] . '%');
        }

        if (!empty($search['item_name'])) {
            $items = $items->where('name', 'like', '%' . $search['item_name'] . '%');
        }

        $items = $items->orderBy('created_at', 'desc')->get();
        
        return $items;
    }
}
