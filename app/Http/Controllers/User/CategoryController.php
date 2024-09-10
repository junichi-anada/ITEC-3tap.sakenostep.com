<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
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
        // var_dump($auth);

        // 現在のユーザーのお気に入り商品を取得
        $categories = ItemCategory::where('site_id', $auth->site_id)->get();

        return view('user.category', compact('categories'));
    }
}
