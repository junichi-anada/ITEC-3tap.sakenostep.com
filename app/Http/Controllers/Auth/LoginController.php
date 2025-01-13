<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * リダイレクト先
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * ユーザーが認証された後の処理
     *
     * @param Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        Log::info('User authenticated:', [
            'user_id' => $user->id,
            'session' => session()->all()
        ]);

        // LINE連携のパラメータがセッションにある場合
        if ($linkParams = session('line_account_link')) {
            // セッションから連携パラメータを削除
            session()->forget('line_account_link');
            
            Log::info('Redirecting to LINE account link:', [
                'params' => $linkParams
            ]);

            // 連携処理用のURLにリダイレクト
            return redirect()->route('line.account.link', [
                'site_code' => $linkParams['site_code'],
                'nonce' => $linkParams['nonce'],
                'link_token' => $linkParams['link_token']
            ]);
        }

        // 通常のリダイレクト処理
        return redirect()->intended($this->redirectTo);
    }

    /**
     * ログイン試行前の処理
     *
     * @param Request $request
     * @return void
     */
    protected function attemptLogin(Request $request)
    {
        // LINE連携パラメータがある場合はセッションに保持
        if ($request->has(['site_code', 'nonce', 'link_token'])) {
            $linkParams = [
                'site_code' => $request->input('site_code'),
                'nonce' => $request->input('nonce'),
                'link_token' => $request->input('link_token')
            ];
            session(['line_account_link' => $linkParams]);
            
            Log::info('Login attempt with LINE params:', [
                'session' => session()->all(),
                'request' => $request->all()
            ]);
        }

        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }
} 