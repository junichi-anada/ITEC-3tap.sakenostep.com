<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\FavoriteItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RecommendedItemController extends Controller
{
    /**
     * おすすめ商品の一覧表示
     */
    public function index()
    {
        // 現在のサイトに登録されているおすすめ商品を取得
        $recommendedItems = Item::where('is_recommended', true)->where('site_id', 1)->get();

        $auth = Auth::user();

        // ユーザーのお気に入り商品を取得
        $favoriteItems = FavoriteItem::where('user_id', $auth->id)->pluck('item_id')->all();

        // ビューで表示する
        return view('user.recommend_items', compact('recommendedItems', 'favoriteItems'));
    }

    /**
     * おすすめ商品を「お気に入り」に登録する機能
     */
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        // すでにお気に入りに登録されているかチェック
        $existingFavorite = FavoriteItem::where('user_id', Auth::id())
            ->where('item_id', $request->item_id)
            ->first();

        if ($existingFavorite) {
            return redirect()->back()->with('error', 'この商品はすでにお気に入りに登録されています。');
        }

        // お気に入りに追加
        FavoriteItem::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
        ]);

        return redirect()->route('user.recommendations.index')->with('success', '商品がお気に入りに追加されました。');
    }

    /**
     * おすすめ商品を「注文リスト」に登録する機能
     */
    public function addToOrderList(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        // 新しい注文リストの作成（必要に応じてOrderやOrderDetailsモデルを使用）
        // ここでは簡単な例として、Orderモデルに直接追加します。
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => 0,  // 実際の計算は省略
            'ordered_at' => now(),
        ]);

        // 商品の追加
        $order->items()->attach($request->item_id, ['quantity' => 1]); // 1個として追加

        return redirect()->route('user.recommendations.index')->with('success', '商品が注文リストに追加されました。');
    }

    /**
     * おすすめ商品からの削除
     */
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item || !$item->is_recommended) {
            return redirect()->route('user.recommendations.index')->with('error', 'おすすめ商品が見つかりません。');
        }

        // おすすめから削除
        $item->update(['is_recommended' => false]);

        return redirect()->route('user.recommendations.index')->with('success', '商品がおすすめから削除されました。');
    }
}
