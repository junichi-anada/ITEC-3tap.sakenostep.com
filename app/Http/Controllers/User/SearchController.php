<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FavoriteItem;
use App\Models\Item;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    // 検索結果の一覧表示
    public function index(Request $request)
    {
        // 検索キーワードのバリデート
        $request->validate([
            'keyword' => 'nullable|string|max:255',
        ]);

        $keyword = $request->input('keyword');

        $auth = Auth::user();

        // 検索キーワードが入力されている場合
        if (!empty($keyword)) {
            // サイトIDが必須で、名前、説明、カテゴリ名のいずれかに検索キーワードが含まれる商品を取得
            $items = Item::where('site_id', $auth->site_id) // サイトIDの条件を必須とする
                         ->where(function ($query) use ($keyword) { // 検索キーワードの条件をグループ化
                         $query->where('name', 'like', "%$keyword%")
                           ->orWhere('item_code', 'like', "%$keyword%")
                           ->orWhere('description', 'like', "%$keyword%")
                           ->orWhereHas('category', function ($query) use ($keyword) {
                               $query->where('name', 'like', "%$keyword%");
                           });
             })
             ->get();
        } else {
            // 検索キーワードが入力されていない場合は全商品を取得
            $items = Item::where('site_id', $auth->site_id)
                        ->get();
        }

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
        })->select('item_id', 'detail_code')->get()->toArray();


        // ビューで表示する
        return view('user.search', compact('items', 'keyword', 'favoriteItems', 'unorderedItems'));
    }
}
