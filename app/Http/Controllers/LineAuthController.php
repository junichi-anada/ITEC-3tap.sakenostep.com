<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

class LineAuthController extends Controller
{
    // LINE認証ページへのリダイレクト
    public function redirectToLine()
    {
        return Socialite::driver('line')->redirect();
    }

    // LINEからのコールバック処理
    public function handleLineCallback()
    {
        try {

            $lineUser = Socialite::driver('line')->user();

            // ユーザーを検索または作成
            $user = AuthenticateOauth::firstOrCreate(
                ['line_id' => $lineUser->id],
                ['name' => $lineUser->name, 'avatar' => $lineUser->avatar]
            );

            // ユーザーをログイン状態にする
            auth()->login($user, true);

            return redirect()->route('dashboard'); // 認証後にダッシュボードへリダイレクト

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['login' => 'LINEログインに失敗しました。']);
        }
    }

    // ユーザーを検索または作成する処理
    private function findOrCreateUser($lineUser)
    {
        return User::firstOrCreate(
            ['line_id' => $lineUser->id],
            [
                'name' => $lineUser->name,
                'avatar' => $lineUser->avatar,
                'email' => $lineUser->email, // LINEからのメールは未提供の場合があります
            ]
        );
    }


}
