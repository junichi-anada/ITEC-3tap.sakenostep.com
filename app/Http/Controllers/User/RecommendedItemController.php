<?php
/**
 * 一般ユーザー向けおすすめ商品管理機能
 * 一般ユーザーのおすすめ商品管理に関する処理を行うコントローラー
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FavoriteItem;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendedItemController extends Controller
{
    /**
     * おすすめ商品の一覧表示
     * おすすめ商品の一覧を表示する。
     * それに加えて、注文リストへの登録ボタンとマイリストへの登録ボタンを表示を制御するために、
     * 現状のユーザーのお気に入り商品と未注文リストの商品を取得する。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 現在のサイトに登録されているおすすめ商品を取得
        $recommendedItems = Item::where('is_recommended', true)->where('site_id', 1)->get();

        $auth = Auth::user();

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

        // 現在のサイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        // ビューで表示する
        return view('user.recommend', compact('recommendedItems', 'favoriteItems', 'unorderedItems', 'categories'));
    }
}
