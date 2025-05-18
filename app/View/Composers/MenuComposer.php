<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log; // Logファサードは不要になったので削除

class MenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Log::info('MenuComposer: compose method called'); // ログ出力は不要になったので削除

        $authenticatedUser = Auth::user(); // Authenticateモデルのインスタンスを取得

        $userName = null;
        if ($authenticatedUser && $authenticatedUser->entity) {
            // entityリレーションを使って関連するモデル（Userモデルを想定）を取得
            $relatedEntity = $authenticatedUser->entity;

            // 関連エンティティがUserモデルであり、name属性を持っているか確認
            if ($relatedEntity instanceof \App\Models\User && isset($relatedEntity->name)) {
                $userName = $relatedEntity->name; // Userモデルから名前を取得
            }
        }

        // Log::info('MenuComposer: Auth::user() result', ['user' => $authenticatedUser]); // ログ出力は不要になったので削除
        // Log::info('MenuComposer: userName value', ['userName' => $userName]); // ログ出力は不要になったので削除

        $view->with('userName', $userName);
    }
}
