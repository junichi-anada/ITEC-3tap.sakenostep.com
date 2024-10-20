<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // 注文についてのページ
    public function order()
    {
        // 現在のサイトのIDを取得
        $auth = Auth::user();

        // 現在のサイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        // 注文についてのページを表示
        return view('user.about_order', compact('categories'));
    }

    // 配送についてのページ
    public function delivery()
    {
        // 現在のサイトのIDを取得
        $auth = Auth::user();

        // 現在のサイトのカテゴリ一覧を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        // 配送についてのページを表示
        return view('user.about_delivery', compact('categories'));
    }
}
