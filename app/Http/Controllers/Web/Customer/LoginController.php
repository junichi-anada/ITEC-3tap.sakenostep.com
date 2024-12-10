<?php
/**
 * 顧客ログインコントローラー
 *
 * @category コントローラー
 * @package App\Http\Controllers\Web\Customer
 * @version 1.0
 */
namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;

class LoginController extends Controller
{

    public function index(Request $request)
    {
        // LinkToken持ってるかも、あればLine連携しにきたユーザー
        $linkToken = $request->input('link_token');

        // ログインしている場合は、ユーザー情報を取得
        if (Auth::check()) {
            $user = Auth::user();
        }

        // ログインしていない かつ リンクトークンがある場合 → Line連携しにきたユーザー
        if (!Auth::check() && $linkToken) {
            // LinkTokenを付けてViewに渡す
            return view('customer.index', ['linkToken' => $linkToken]);
        }

        // ログインしていない かつ リンクトークンがない場合 → 通常のログイン
        if (!Auth::check() && !$linkToken) {
            // 普通のログイン画面へ
            return view('customer.index');
        }

        // ログインしている かつ リンクトークンがない場合 → 通常のログイン
        if (Auth::check() && !$linkToken) {
            // ログイン後、注文画面へ
            return redirect()->route('user.order.item.list');
        }

        // ログインしている かつ リンクトークンがある場合 → Line連携しにきたユーザー
        if (Auth::check() && $linkToken) {
            // 一旦ログアウト
            Auth::logout();
            // LinkTokenを付けてViewに渡す
            return view('customer.index', ['linkToken' => $linkToken]);
        }

        // ここに来ることはないはず
        return view('customer.index');
    }

    // public function login(Request $request)
    // {
    //     // バリデーション
    //     $credentials = $this->validateLogin($request);

    //     try {
    //         // サイトの存在確認
    //         $site = $this->authService->validateSite($credentials['site_code']);

    //         // ユーザー認証
    //         $auth = $this->authService->authenticateUser($credentials['login_code'], $site->id, $credentials['password']);

    //         // ログイン処理
    //         Auth::login($auth);
    //         $auth->touch();
    //         $request->session()->regenerate();

    //         // 念のため、サイト管理者がこちらから来る可能性もあるので、
    //         // サイト管理者の場合は、ログイン後、サイト管理者画面へ（LinkTokenは無視）
    //         if ($auth->entity_type === 'App\Models\Operator') {
    //             return redirect()->route('operator.dashboard');
    //         }

    //         // LinkTorkenがある場合は、Line連携しにきたユーザー
    //         if ($request->input('link_token')) {
    //             // nonceを生成してLINE連携画面へ
    //             $nonce = $this->generateNonce();

    //             // 出来上がったnonceをLineUserテーブルに登録しておく
    //             $lineUser = LineUser::create([
    //                 'site_id' => $site->id,
    //                 'user_id' => $auth->id,
    //                 'nonce' => $nonce,
    //             ]);

    //             // LINE連携画面へ
    //             return redirect()->away('https://access.line.me/dialog/bot/accountLink?linkToken='.$linkToken.'&nonce='.$nonce);
    //         }

    //         // 顧客側ログイン
    //         return redirect()->route('user.order.item.list');

    //     } catch (ValidationException $e) {
    //         return back()->withErrors($e->errors());
    //     } catch (\Exception $e) {
    //         return back()->withErrors(['error' => 'ログインに失敗しました。']);
    //     }
    // }

    // public function logout(Request $request)
    // {
    //     // Authenticateのentity_typeを取得
    //     $entityType = Auth::user()->entity_type;

    //     // ログアウト
    //     Auth::guard('web')->logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     // ログアウト後、ログイン画面へ
    //     if ($entityType === 'App\Models\User') {
    //         return redirect()->route('customer.index');
    //     } elseif ($entityType === 'App\Models\Operator') {
    //         return redirect()->route('operator.index');
    //     }
    //     return redirect('/');
    // }

    // private function validateLogin(Request $request)
    // {
    //     return $request->validate([
    //         'login_code' => 'required|string',
    //         'password' => 'required|string',
    //         'site_code' => 'required|string',
    //     ]);
    // }

}
