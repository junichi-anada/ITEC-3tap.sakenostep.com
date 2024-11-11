<?php

namespace Tests\Feature\Ajax\Customer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\FavoriteItem;
use App\Models\Site;
use App\Models\Company;

class FavoriteItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の会社とサイトを作成
        $company = Company::factory()->create();
        $site = Site::factory()->create(['company_id' => $company->id]);

        // テスト用のユーザーとアイテムを作成
        $this->user = User::factory()->create(['site_id' => $site->id]);
        $this->item = Item::factory()->create(['site_id' => $site->id]);

        // 事前にお気に入りに追加
        FavoriteItem::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'site_id' => $this->user->site_id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_add_item_to_favorites_first_time()
    {
        $this->actingAs($this->user, 'web');

        // 事前にお気に入りをクリア
        FavoriteItem::where('user_id', $this->user->id)->delete();

        $response = $this->postJson(route('user.favorite.item.add'), [
            'item_code' => $this->item->item_code,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'お気に入りに追加しました']);

        $this->assertDatabaseHas('favorite_items', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_cannot_add_item_to_favorites_if_already_added()
    {
        $this->actingAs($this->user, 'web');

        // すでに登録済みの状態で再度追加
        $response = $this->postJson(route('user.favorite.item.add'), [
            'item_code' => $this->item->item_code,
        ]);

        $response->assertStatus(409) // 既に登録済みの場合のステータスコード
                 ->assertJson(['message' => 'すでにお気に入りに追加されています']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_re_add_item_to_favorites_after_removal()
    {
        $this->actingAs($this->user, 'web');

        // 一度削除
        $this->deleteJson(route('user.favorite.item.remove', $this->item->item_code));

        // 再度追加
        $response = $this->postJson(route('user.favorite.item.add'), [
            'item_code' => $this->item->item_code,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'お気に入りに追加しました']);

        $this->assertDatabaseHas('favorite_items', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_remove_item_from_favorites()
    {
        $this->actingAs($this->user, 'web');

        // 商品コードをログに出力して確認
        $itemCode = $this->item->item_code;
        \Log::info("Attempting to delete item with code: $itemCode for user: {$this->user->id}");

        $response = $this->deleteJson(route('user.favorite.item.remove', $itemCode));

        $response->assertStatus(200)
                 ->assertJson(['message' => 'マイリストから削除しました']);

        $this->assertSoftDeleted('favorite_items', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);
    }
}
