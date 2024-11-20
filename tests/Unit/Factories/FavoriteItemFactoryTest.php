<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\FavoriteItem;
use App\Models\User;
use App\Models\Item;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * FavoriteItemFactoryのテストクラス
 */
final class FavoriteItemFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * FavoriteItemFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_favorite_item()
    {
        $favoriteItem = FavoriteItem::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('favorite_items', [
            'id' => $favoriteItem->id,
            'user_id' => $favoriteItem->user_id,
            'item_id' => $favoriteItem->item_id,
            'site_id' => $favoriteItem->site_id,
        ]);

        // 属性が期待通りであることを確認
        $this->assertInstanceOf(User::class, $favoriteItem->user);
        $this->assertInstanceOf(Item::class, $favoriteItem->item);
        $this->assertInstanceOf(Site::class, $favoriteItem->site);
    }

    /**
     * FavoriteItemFactoryが特定のユーザーに関連付けられたお気に入りアイテムを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_favorite_item_for_specific_user()
    {
        $user = User::factory()->create();
        $favoriteItem = FavoriteItem::factory()->forUser($user)->create();

        $this->assertEquals($user->id, $favoriteItem->user_id);
        $this->assertDatabaseHas('favorite_items', [
            'id' => $favoriteItem->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * FavoriteItemFactoryが特定のアイテムに関連付けられたお気に入りアイテムを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_favorite_item_for_specific_item()
    {
        $item = Item::factory()->create();
        $favoriteItem = FavoriteItem::factory()->forItem($item)->create();

        $this->assertEquals($item->id, $favoriteItem->item_id);
        $this->assertDatabaseHas('favorite_items', [
            'id' => $favoriteItem->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * FavoriteItemFactoryが特定のサイトに関連付けられたお気に入りアイテムを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_favorite_item_for_specific_site()
    {
        $site = Site::factory()->create();
        $favoriteItem = FavoriteItem::factory()->forSite($site)->create();

        $this->assertEquals($site->id, $favoriteItem->site_id);
        $this->assertDatabaseHas('favorite_items', [
            'id' => $favoriteItem->id,
            'site_id' => $site->id,
        ]);
    }

    /**
     * FavoriteItemFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_favorite_item_with_multiple_states()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $site = Site::factory()->create();

        $favoriteItem = FavoriteItem::factory()
            ->forUser($user)
            ->forItem($item)
            ->forSite($site)
            ->create();

        $this->assertEquals($user->id, $favoriteItem->user_id);
        $this->assertEquals($item->id, $favoriteItem->item_id);
        $this->assertEquals($site->id, $favoriteItem->site_id);

        $this->assertDatabaseHas('favorite_items', [
            'id' => $favoriteItem->id,
            'user_id' => $user->id,
            'item_id' => $item->id,
            'site_id' => $site->id,
        ]);
    }
}
