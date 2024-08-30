<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavoriteItem;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteItemController extends Controller
{
    /**
     * お気に入り商品の一覧表示
     */
    public function index()
    {
        // 現在のユーザーのお気に入り商品を取得
        $favoriteItems = FavoriteItem::where('user_id', Auth::id())->with('item')->get();

        // Viewで表示する
        return view('user.favorite_items', ['favoriteItems' => $favoriteItems]);

        // return response()->json($favoriteItems);
    }

    /**
     * お気に入り商品への登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'site_id' => 'required|exists:sites,id',
        ]);

        $favoriteItem = FavoriteItem::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'site_id' => $request->site_id,
        ]);

        return response()->json(['message' => 'お気に入りに追加しました', 'favoriteItem' => $favoriteItem], 201);
    }

    /**
     * お気に入り商品からの削除
     */
    public function destroy($id)
    {
        $favoriteItem = FavoriteItem::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$favoriteItem) {
            return response()->json(['message' => 'お気に入り商品が見つかりません'], 404);
        }

        $favoriteItem->delete();

        return response()->json(['message' => 'お気に入りから削除しました']);
    }

}
