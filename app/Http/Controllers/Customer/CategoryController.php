<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FavoriteItem;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * index
     * カテゴリ一覧表示
     *
     * @return void
     */
    public function index()
    {
        $auth = Auth::user();

        // 該当サイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        return view('user.category', compact('categories'));
    }

    /**
     * show
     * カテゴリに属する商品一覧表示
     *
     * @param  $code
     * @return void
     */
    public function show($code)
    {
        $auth = Auth::user();

        // 該当サイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        // カテゴリに所属する商品一覧を取得
        $category = ItemCategory::where('site_id', $auth->site_id)->where('category_code', $code)->first();
        $items = Item::where('site_id', $auth->site_id)->where('category_id', $category->id)->get();

        // ログインしているサイトでのユーザーのお気に入り商品を取得
        $favoriteItems = FavoriteItem::where('user_id', $auth->id)
                                     ->where('site_id', $auth->site_id)
                                     ->pluck('item_id')
                                     ->all();

        // ログインしているサイトでのユーザーの未注文リストの商品を取得
        $unorderedItems = OrderDetail::whereHas('order', function ($query) use ($auth) {
            $query->where('user_id', $auth->id)
                ->where('site_id', $auth->site_id)
                ->whereNull('ordered_at');  // 未注文の条件
        })->select('item_id', 'detail_code', 'volume')->get()->toArray();


        return view('user.category_item', compact('category', 'items', 'unorderedItems', 'favoriteItems', 'categories'));
    }
}
