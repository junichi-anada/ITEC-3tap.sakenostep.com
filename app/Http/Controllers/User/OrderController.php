<?php
/**
 * 一般ユーザー向け注文管理機能
 * 一般ユーザーの注文管理に関する処理を行うコントローラー
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * index
     * 未注文商品リストの一覧表示
     * 未発注の注文に登録されている商品一覧を表示する。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $auth = Auth::user();

        // 未発注の注文を取得
        $order = Order::where('user_id', $auth->id)
                      ->where('site_id', $auth->site_id)
                      ->whereNull('ordered_at') // 未発注のみ
                      ->first();

        // 未発注の注文がない場合、空のリストを渡す
        $orderItems = [];

        if ($order) {
            // 注文に関連する注文詳細と商品情報を取得
            $orderItems = OrderDetail::with('item') // itemリレーションをロード
                                     ->where('order_id', $order->id)
                                     ->get();
        }

        // ビューで表示する
        return view('user.order', compact('orderItems'));
    }


    /**
     * 注文リストへの登録
     * 注文リストに商品を追加する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'site_id' => 'required|exists:sites,id',
            'volume' => 'nullable|integer|min:1', // 必要に応じて volume をリクエストから受け取る
        ]);

        $auth = Auth::user();
        $volume = $request->input('volume', 1); // volume をリクエストから取得（デフォルトは1）

        // トランザクションの開始
        DB::beginTransaction();

        try {
            // 未発注の注文リストを取得
            $order = Order::where('user_id', $auth->id)
                        ->where('site_id', $request->site_id)
                        ->whereNull('ordered_at')
                        ->first();

            // 未発注の注文リストがない場合、新しく作成
            if (!$order) {
                do {
                    $orderCode = Str::ulid();  // ULIDの生成
                } while (Order::where('order_code', $orderCode)->exists());

                $order = Order::create([
                    'order_code' => $orderCode,
                    'site_id' => $request->site_id,
                    'user_id' => $auth->id,
                ]);
            }

            // 商品情報の取得
            $item = Item::find($request->item_id);

            // ソフトデリートされている注文詳細の復元
            $orderDetail = OrderDetail::withTrashed()->where('order_id', $order->id)
                ->where('item_id', $item->id)
                ->first();

            if ($orderDetail) {
                // 注文詳細がソフトデリートされている場合、復元
                $orderDetail->restore();

                // 必要ならば、量や価格の更新も行う
                $orderDetail->update([
                    'volume' => $volume,
                    'unit_price' => $item->unit_price,
                    'unit_name' => $item->name,
                    'price' => $item->unit_price * $volume,
                    'tax' => $item->unit_price * $volume * 0.1, // 例として10%の消費税を適用
                ]);

            } else {
                // ソフトデリートされていない場合、新規作成
                do {
                    $detailCode = Str::ulid();  // ULIDの生成
                } while (OrderDetail::where('detail_code', $detailCode)->exists());

                // 注文詳細の追加
                $orderDetail = OrderDetail::create([
                    'detail_code' => $detailCode,
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'volume' => $volume,
                    'unit_price' => $item->unit_price,
                    'unit_name' => $item->name,
                    'price' => $item->unit_price * $volume,
                    'tax' => $item->unit_price * $volume * 0.1, // 例として10%の消費税を適用
                ]);
            }

            // トランザクションのコミット
            DB::commit();

            return response()->json(['message' => '注文リストに追加しました', 'detail_code' => $orderDetail->detail_code], 201);

        } catch (\Exception $e) {
            // エラーが発生した場合はトランザクションをロールバック
            DB::rollBack();
            return response()->json(['message' => '注文リストに追加できませんでした', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 注文詳細の指定削除
     * パラメータのdetailCodeに一致する注文詳細を削除する。
     *
     * @param string $detailCode
     */
    public function destroy(Request $request, $detailCode)
    {
        // 直接バリデーションを実行
        if (!is_string($detailCode) || strlen($detailCode) !== 26) { // ULIDは26文字の文字列
            return response()->json(['message' => '無効な注文詳細コードです'], 400);
        }

        $auth = Auth::user();

        // 対象の注文詳細情報を取得
        // 不正アクセス防止のため、該当ユーザーの未発注注文に紐づく注文詳細のみを対象とする
        $orderDetail = OrderDetail::whereHas('order', function ($query) use ($auth) {
                $query->where('user_id', $auth->id)
                      ->where('site_id', $auth->site_id)
                      ->whereNull('ordered_at'); // 未発注のみ対象
            })
            ->where('detail_code', $detailCode)
            ->first();

        // 該当する注文詳細が存在しない場合の処理
        if (!$orderDetail) {
            return response()->json(['message' => '注文詳細データが見つかりません'], 404);
        }

        // ソフトデリートによる注文詳細の削除
        $orderDetail->delete();

        return response()->json(['message' => '注文リストから削除しました']);
    }

}
