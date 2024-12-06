<?php

namespace App\Services\FavoriteItem\Traits;

trait FavoriteItemConditionTrait
{
    /**
     * お気に入り商品の検索条件を構築する
     *
     * @param int $userId ユーザーID
     * @param int|null $itemId 商品ID
     * @param int|null $siteId サイトID
     * @return array<string, int> 検索条件
     */
    protected function buildFavoriteItemConditions(
        int $userId,
        ?int $itemId = null,
        ?int $siteId = null
    ): array {
        $conditions = ['user_id' => $userId];

        if ($itemId !== null) {
            $conditions['item_id'] = $itemId;
        }

        if ($siteId !== null) {
            $conditions['site_id'] = $siteId;
        }

        return $conditions;
    }

    /**
     * お気に入り商品の一覧取得条件を構築する
     *
     * @param array $conditions 基本条件
     * @param array $orderBy ソート条件
     * @param array $with イーガーロード設定
     * @param bool $withTrashed 削除済みデータを含むか
     * @return array 検索条件
     */
    protected function buildListConditions(
        array $conditions,
        array $orderBy = ['created_at' => 'desc'],
        array $with = [],
        bool $withTrashed = false
    ): array {
        return [
            'conditions' => $conditions,
            'orderBy' => $orderBy,
            'with' => $with,
            'containTrash' => $withTrashed
        ];
    }
}
