<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        return view('index');
    }

    public function login(Request $request)
    {
        $credentials = $this->validateLogin($request);

        try {
            $site = $this->authService->validateSite($credentials['site_code']);

            $auth = $this->authService->authenticateUser($credentials['login_code'], $site->id, $credentials['password']);

            return $this->handleUserWasAuthenticated($request, $auth);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ログインに失敗しました。']);
        }
    }

    public function logout(Request $request)
    {
        // Authenticateのentity_typeを取得
        $entityType = Auth::user()->entity_type;

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ログアウト後、ログイン画面へ
        if ($entityType === 'App\Models\User') {
            return redirect()->route('customer.index');
        } elseif ($entityType === 'App\Models\Operator') {
            return redirect()->route('operator.index');
        }
        return redirect('/login');
    }

    private function validateLogin(Request $request)
    {
        return $request->validate([
            'login_code' => 'required|string',
            'password' => 'required|string',
            'site_code' => 'required|string',
        ]);
    }

    private function handleUserWasAuthenticated(Request $request, $auth)
    {
        Auth::login($auth);
        $auth->touch();
        $request->session()->regenerate();

        if (!Auth::check()) {
            return redirect('/login')->withErrors(['error' => 'ログインに失敗しました。']);
        }

        // LinkTokenチェック
        if (!empty($request->link_token)) {
            $nonce = $this->generateNonce();

            // 出来上がったnonceをLineUserテーブルに登録しておく
            $lineUser = LineUser::create([
                'site_id' => $site->id,
                'user_id' => $auth->id,
                'nonce' => $nonce,
            ]);

            return redirect()->away('https://access.line.me/dialog/bot/accountLink?linkToken='.$linkToken.'&nonce='.$nonce);
        }

        // ログイン後、それぞれのメニュー画面へ
        if ($auth->entity_type === 'App\Models\User') {
            return redirect()->intended(route('user.order.item.list'));
        } elseif ($auth->entity_type === 'App\Models\Operator') {
            return redirect()->intended(route('operator.dashboard'));
        }

        return redirect()->intended(route('user.order.item.list'));
    }

    private function generateNonce()
    {
        return bin2hex(random_bytes(16));
    }

}
