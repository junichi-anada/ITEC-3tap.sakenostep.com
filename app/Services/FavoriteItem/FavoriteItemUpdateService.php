<?php

namespace App\Services\FavoriteItem;

use App\Models\FavoriteItem;
use App\Models\User;
use App\Models\Item;
use App\Models\Site;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FavoriteItemUpdateService
{
    /**
     * お気に入り商品情報を更新する
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @return FavoriteItem
     * @throws ValidationException
     */
    public function execute(int $userId, int $itemId, int $siteId): FavoriteItem
    {
        // バリデーション
        $this->validate($userId, $itemId, $siteId);

        // お気に入り商品情報の更新
        $favoriteItem = FavoriteItem::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('site_id', $siteId)
            ->firstOrFail();

        // 更新処理（必要に応じてフィールドを更新）
        $favoriteItem->updated_at = now();
        $favoriteItem->save();

        return $favoriteItem;
    }

    /**
     * バリデーションを行う
     *
     * @param int $userId
     * @param int $itemId
     * @param int $siteId
     * @throws ValidationException
     */
    protected function validate(int $userId, int $itemId, int $siteId): void
    {
        $validator = Validator::make(compact('userId', 'itemId', 'siteId'), [
            'userId' => 'required|exists:users,id',
            'itemId' => 'required|exists:items,id',
            'siteId' => 'required|exists:sites,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
