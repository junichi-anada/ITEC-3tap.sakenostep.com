<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Mail\OrderCompletedForCustomer;
use App\Mail\OrderNotificationForOperator;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Operator;
use App\Models\Order; // Order を use
use App\Models\Site;
// use App\Services\Order\Actions\CreateOrderAction; // 不要になるので削除
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderCreationNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 注文作成時にイベントが発行され、メールが送信されることをテスト
     *
     * @return void
     */
    public function test_order_creation_dispatches_event_and_sends_emails(): void
    {
        // 1. Arrange: テストデータの準備
        Event::fake(); // イベントをフェイク
        Mail::fake(); // メールをフェイク

        $site = Site::factory()->create();
        $customer = Customer::factory()->create(['site_id' => $site->id]);
        $operator = Operator::factory()->create(); // 通知を受け取るオペレーター
        $site->operators()->attach($operator, ['role' => 'admin']); // サイトにオペレーターを関連付け

        $item1 = Item::factory()->create(['site_id' => $site->id]);
        $item2 = Item::factory()->create(['site_id' => $site->id]);

        // 2. Act: ファクトリを使って注文と注文明細を作成
        $order = Order::factory()
            ->for($customer) // 顧客を指定
            ->for($site)     // サイトを指定
            ->hasDetails(1, [ // 1つ目の明細
                'item_id' => $item1->id,
                'volume' => 2,
                // 必要に応じて unit_price, price, tax なども指定可能
                // 指定しない場合は OrderDetailFactory の定義が使われる
            ])
            ->hasDetails(1, [ // 2つ目の明細
                'item_id' => $item2->id,
                'volume' => 1,
            ])
            ->create(); // 注文を作成

        // 3. Assert: アサーション
        // OrderCreated イベントが発行されたか
        Event::assertDispatched(OrderCreated::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        // 顧客向けメールが送信されたか
        Mail::assertSent(OrderCompletedForCustomer::class, function ($mail) use ($customer, $order) {
            return $mail->hasTo($customer->email) && $mail->order->id === $order->id;
        });

        // オペレーター向けメールが送信されたか
        Mail::assertSent(OrderNotificationForOperator::class, function ($mail) use ($operator, $order) {
            $hasRecipient = false;
            foreach ($mail->to as $recipient) {
                if ($recipient['address'] === $operator->email) {
                    $hasRecipient = true;
                    break;
                }
            }
            return $hasRecipient && $mail->order->id === $order->id;
        });
    }
}
