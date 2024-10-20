<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ItemCategory;

class HistoryController extends Controller
{
    /**
     * 注文履歴の一覧表示
     * 注文履歴を一覧表示する。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $auth = Auth::user();

        // 注文履歴を取得
        $orders = Order::where('user_id', $auth->id)
                       ->where('site_id', $auth->site_id)
                       ->whereNotNull('ordered_at')
                       ->orderBy('ordered_at', 'desc')
                       ->get();

        // 現在のサイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        // ビューで表示する
        return view('user.history', compact('orders', 'categories'));
    }

    /**
     * 注文履歴の詳細表示
     * 注文履歴の詳細を表示する。
     *
     * @return \Illuminate\View\View
     */
    public function detail($orderCode)
    {
        $auth = Auth::user();

        // 注文履歴を取得
        $order = Order::where('user_id', $auth->id)
                      ->where('site_id', $auth->site_id)
                      ->where('order_code', $orderCode)
                      ->first();

        // 注文履歴に紐づく注文詳細を取得
        $orderItems = OrderDetail::where('order_id', $order->id)->get();

        // 現在のサイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        // ビューで表示する
        return view('user.history_detail', compact('orderItems', 'categories'));
    }
}
