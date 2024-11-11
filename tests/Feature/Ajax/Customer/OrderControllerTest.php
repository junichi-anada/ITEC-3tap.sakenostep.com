<?php

namespace Tests\Feature\Ajax\Customer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Site;
use App\Models\Company;

class OrderControllerTest extends TestCase
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
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_add_item_to_order_list()
    {
        $this->actingAs($this->user, 'web');

        $response = $this->postJson(route('user.order.item.list.add'), [
            'item_code' => $this->item->item_code,
            'volume' => 1,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => '注文リストに追加しました']);

        $this->assertDatabaseHas('order_details', [
            'order_id' => Order::first()->id, // Order ID を確認
            'item_id' => $this->item->id,
            'volume' => 1,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_remove_item_from_order_list()
    {
        $this->actingAs($this->user, 'web');

        $order = Order::factory()->create(['user_id' => $this->user->id, 'site_id' => $this->user->site_id]);
        $orderDetail = OrderDetail::factory()->create([
            'order_id' => $order->id,
            'item_id' => $this->item->id,
        ]);

        $response = $this->deleteJson(route('user.order.item.list.remove', $this->item->item_code));

        $response->assertStatus(200)
                 ->assertJson(['message' => '注文リストから削除しました']);

        $this->assertSoftDeleted('order_details', [
            'id' => $orderDetail->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_remove_all_items_from_order_list()
    {
        $this->actingAs($this->user, 'web');

        $order = Order::factory()->create(['user_id' => $this->user->id, 'site_id' => $this->user->site_id]);
        OrderDetail::factory()->count(3)->create([
            'order_id' => $order->id,
            'item_id' => $this->item->id,
        ]);

        $response = $this->deleteJson(route('user.order.item.list.remove.all'));

        $response->assertStatus(200)
                 ->assertJson(['message' => '注文リストから全て削除しました']);

        $this->assertSoftDeleted('order_details', [
            'order_id' => $order->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_execute_order()
    {
        $this->actingAs($this->user, 'web');

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'site_id' => $this->user->site_id,
        ]);

        $response = $this->postJson(route('user.order.item.list.order'));

        $response->assertStatus(200)
                 ->assertJson(['message' => '注文しました']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'ordered_at' => now(),
        ]);
    }
}
