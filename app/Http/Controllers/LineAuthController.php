<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Authenticate;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Exception;

class LineAuthController extends Controller
{
    /**
     * LINE認証ページへのリダイレクト
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToLine()
    {
        return Socialite::driver('line')->redirect();
    }

    /**
     * LINEからのコールバック処理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleLineCallback()
    {
        try {
            $lineUser = Socialite::driver('line')->user();
            $user = $this->findOrCreateUser($lineUser);

            // ユーザーをログイン状態にする
            auth()->login($user, true);

            // LINE認証情報を保存/更新
            Authenticate::updateOrCreate(
                [
                    'provider_id' => config('services.line.provider_id'),
                    'auth_code' => $lineUser->id,
                ],
                [
                    'entity_type' => User::class,
                    'entity_id' => $user->id,
                    'site_id' => config('services.line.site_id'),
                    'token' => $lineUser->token,
                    'token_expiry' => now()->addDays(30),
                    'expires_at' => now()->addYear(),
                ]
            );

            return redirect()->route('dashboard');
        } catch (Exception $e) {
            Log::error('LINE login failed: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['login' => 'LINEログインに失敗しました。']);
        }
    }

    /**
     * ユーザーを検索または作成する
     *
     * @param \Laravel\Socialite\Two\User $lineUser
     * @return User
     */
    private function findOrCreateUser($lineUser)
    {
        // LINE IDで既存ユーザーを検索
        $authInfo = Authenticate::where('auth_code', $lineUser->id)
            ->where('provider_id', config('services.line.provider_id'))
            ->first();

        if ($authInfo) {
            return User::find($authInfo->entity_id);
        }

        // 新規ユーザーを作成
        return User::create([
            'name' => $lineUser->name ?? 'LINEユーザー',
            'email' => $lineUser->email,
            'password' => bcrypt(str_random(16)), // ランダムなパスワードを設定
        ]);
    }
}
