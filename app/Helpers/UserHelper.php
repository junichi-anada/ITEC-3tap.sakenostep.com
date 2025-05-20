<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User; // Userモデルを使用する場合

if (! function_exists('getCustomerName')) {
    /**
     * Get the name of the authenticated customer.
     *
     * @return string|null
     */
    function getCustomerName(): ?string
    {
        $authenticatedUser = Auth::user(); // Authenticateモデルのインスタンスを取得

        if ($authenticatedUser && $authenticatedUser->entity) {
            $relatedEntity = $authenticatedUser->entity;

            // 関連エンティティがUserモデルであり、name属性を持っているか確認
            if ($relatedEntity instanceof \App\Models\User && isset($relatedEntity->name)) {
                return $relatedEntity->name; // Userモデルから名前を取得
            }
        }

        return null;
    }
}
