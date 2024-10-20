<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\User;
use App\Models\AuthenticateOauth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        // var_dump($auth);

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // Userモジュール
        // サイト単位でのユーザーの総数
        $user_count = User::where('site_id', $auth->site_id)->count();

        // 当日新規顧客の数
        $new_user_count = User::where('created_at', '>=', now()->startOfDay())->count();

        // LINE連携済みのユーザーの数
        $line_user_count = AuthenticateOauth::where('auth_provider_id', 1)->count();

        return view('operator.dashboard', compact('operator', 'user_count', 'new_user_count', 'line_user_count'));
    }

    public function getUserCount()
    {
        $auth = Auth::user();
        $user_count = User::where('site_id', $auth->site_id)->count();
        return response()->json($user_count);
    }
}
