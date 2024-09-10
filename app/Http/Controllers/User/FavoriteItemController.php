<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FavoriteItem;
use App\Models\Item;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteItemController extends Controller
{
    /**
     * index
     * お気に入り商品の一覧表示
     */
    public function index()
    {
        $auth = Auth::user();
        // var_dump($auth);

        // 現在のユーザーのお気に入り商品を取得
        $favoriteItems = FavoriteItem::where('user_id', $auth->id)->where('site_id', $auth->site_id)->with('item')->get();

        // entity リレーションを通じて関連する User モデルを取得
        $entity = $auth->entity;
        // var_dump($entity);

        // ログインしているサイトでのユーザーの未注文リストの商品を取得
        $unorderedItems = OrderDetail::whereHas('order', function ($query) use ($auth) {
            $query->where('user_id', $auth->id)
                ->where('site_id', $auth->site_id)
                ->whereNull('ordered_at');  // 未注文の条件
        })->select('item_id', 'detail_code', 'volume')->get()->toArray();

        // Viewで表示する
        return view('user.favorite', compact('favoriteItems', 'entity', 'unorderedItems'));

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

        $auth = Auth::user();

        // ソフトデリートを含む検索を実施
        $favoriteItem = FavoriteItem::withTrashed()->where([
            'user_id' => $auth->id,
            'item_id' => $request->item_id,
            'site_id' => $request->site_id,
        ])->first();

        if ($favoriteItem) {
            if ($favoriteItem->trashed()) {
                // ソフトデリートされている場合は復元
                $favoriteItem->restore();
                return response()->json(['message' => 'マイリストに追加しました', 'favoriteItem' => $favoriteItem], 200);
            } else {
                // 既にお気に入りに存在する場合の対応（何もしないかエラーメッセージを返す）
                return response()->json(['message' => '既にマイリストに追加されています'], 200);
            }
        }

        $favoriteItem = FavoriteItem::updateOrCreate([
            'user_id' => $auth->id,
            'item_id' => $request->item_id,
            'site_id' => $request->site_id,
        ]);

        return response()->json(['message' => 'マイリストに追加しました', 'favoriteItem' => $favoriteItem], 201);
    }

    /**
     * お気に入り商品からの削除
     */
    public function destroy($id, Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
        ]);

        $auth = Auth::user();

        $favoriteItem = FavoriteItem::where('item_id', $id)->where('user_id', $auth->id)->where('site_id', $request->site_id);

        if (!$favoriteItem) {
            return response()->json(['message' => '対象の商品が見つかりません'], 404);
        }

        $favoriteItem->delete();

        return response()->json(['message' => 'マイリストから削除しました']);
    }

}
