<?php

namespace Database\Factories;

use App\Models\FavoriteItem;
use App\Models\User;
use App\Models\Item;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * FavoriteItemFactory
 *
 * このファクトリは、ユーザーがお気に入りとしてマークしたアイテムを生成します。
 * 主な仕様:
 * - ユーザーID、アイテムID、サイトIDを関連付けて生成します。
 * 制限事項:
 * - 存在しないユーザーやアイテム、サイトへの関連付けは行いません。
 */
class FavoriteItemFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = FavoriteItem::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'site_id' => Site::factory(),
        ];
    }

    /**
     * 特定のユーザーに関連付けられたお気に入りアイテムを生成
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * 特定のアイテムに関連付けられたお気に入りを生成
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forItem(Item $item): static
    {
        return $this->state(fn (array $attributes) => [
            'item_id' => $item->id,
        ]);
    }

    /**
     * 特定のサイトに関連付けられたお気に入りを生成
     *
     * @param \App\Models\Site $site
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSite(Site $site): static
    {
        return $this->state(fn (array $attributes) => [
            'site_id' => $site->id,
        ]);
    }
}
