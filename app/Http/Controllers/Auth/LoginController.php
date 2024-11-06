<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Authenticate;
use App\Models\Site;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * ログインフォームの表示
     */
    public function showLoginForm()
    {
        return view('index'); // ログインフォームのビューを指定
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login_code' => 'required|string',
            'password' => 'required|string',
            'site_code' => 'required|string',
        ]);

        // site_code から site_id を取得
        $site = Site::where('site_code', $credentials['site_code'])->first();

        if (!$site) {
            throw ValidationException::withMessages([
                'site_code' => __('無効なサイトコードです。'),
            ]);
        }

        // login_code と site_id で Authenticate を検索
        $auth = Authenticate::where('login_code', $credentials['login_code'])
                            ->where('site_id', $site->id)
                            ->first();

        // 認証の確認
        if ($auth && Hash::check($credentials['password'], $auth->password)) {
            // 認証成功
            Auth::login($auth);

            //updated_atを更新
            $auth->touch();

            $request->session()->regenerate();

            // entity_type に応じてリダイレクト先を変更
            if ($auth->entity_type === 'App\Models\User') {
                return redirect()->intended(route('user.order.item.list')); // ログイン後のリダイレクト先
            } elseif ($auth->entity_type === 'App\Models\Operator') {
                return redirect()->intended(route('operator.dashboard')); // ログイン後のリダイレクト先
            }

            return redirect()->intended(route('user.order.item.list')); // ログイン後のリダイレクト先
        }

        throw ValidationException::withMessages([
            'login_code' => __('電話番号またはお客様番号に誤りがあります。'),
        ]);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // ログアウト後のリダイレクト先
    }

}
