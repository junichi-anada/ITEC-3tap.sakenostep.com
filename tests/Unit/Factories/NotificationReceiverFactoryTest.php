<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\NotificationReceiver;
use App\Models\Notification;
use App\Models\NotificationSendMethod;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * NotificationReceiverFactoryのテストクラス
 */
final class NotificationReceiverFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NotificationReceiverFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification_receiver()
    {
        $notificationReceiver = NotificationReceiver::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('notification_receivers', [
            'id' => $notificationReceiver->id,
            'notification_id' => $notificationReceiver->notification_id,
            'entity_type' => $notificationReceiver->entity_type,
            'entity_id' => $notificationReceiver->entity_id,
            'send_method_id' => $notificationReceiver->send_method_id,
            'sent_at' => $notificationReceiver->sent_at,
            'is_read' => $notificationReceiver->is_read,
            'read_at' => $notificationReceiver->read_at,
        ]);

        // 属性が期待通りであることを確認
        $this->assertContains($notificationReceiver->entity_type, ['App\Models\User', 'App\Models\Company', 'App\Models\Operator']);
        $this->assertNotNull($notificationReceiver->notification_id);
        $this->assertNotNull($notificationReceiver->entity_id);
        $this->assertNotNull($notificationReceiver->send_method_id);
        $this->assertIsBool($notificationReceiver->is_read);
        if ($notificationReceiver->is_read) {
            $this->assertNotNull($notificationReceiver->read_at);
        } else {
            $this->assertNull($notificationReceiver->read_at);
        }
    }

    /**
     * NotificationReceiverFactoryが特定の送信方法で受信者を生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification_receiver_with_specific_send_method()
    {
        $sendMethod = NotificationSendMethod::factory()->create(['name' => 'メール']);
        $notificationReceiver = NotificationReceiver::factory()->withSendMethod($sendMethod)->create();

        $this->assertEquals($sendMethod->id, $notificationReceiver->send_method_id);
        $this->assertDatabaseHas('notification_receivers', [
            'id' => $notificationReceiver->id,
            'send_method_id' => $sendMethod->id,
        ]);
    }

    /**
     * NotificationReceiverFactoryが既読状態の受信者を生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_read_notification_receiver()
    {
        $notificationReceiver = NotificationReceiver::factory()->read()->create();

        $this->assertTrue($notificationReceiver->is_read);
        $this->assertNotNull($notificationReceiver->read_at);
        $this->assertGreaterThanOrEqual($notificationReceiver->sent_at, $notificationReceiver->read_at);
    }

    /**
     * NotificationReceiverFactoryが未読状態の受信者を生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unread_notification_receiver()
    {
        $notificationReceiver = NotificationReceiver::factory()->unread()->create();

        $this->assertFalse($notificationReceiver->is_read);
        $this->assertNull($notificationReceiver->read_at);
    }

    /**
     * NotificationReceiverFactoryが特定の送信方法で受信者を生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_receiver_for_specific_send_method()
    {
        $sendMethod = NotificationSendMethod::factory()->create(['name' => 'SMS']);
        $notificationReceiver = NotificationReceiver::factory()->withSendMethod($sendMethod)->create();

        $this->assertEquals($sendMethod->id, $notificationReceiver->send_method_id);
        $this->assertDatabaseHas('notification_receivers', [
            'id' => $notificationReceiver->id,
            'send_method_id' => $sendMethod->id,
        ]);
    }

    /**
     * NotificationReceiverFactoryが関連するエンティティを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_entity()
    {
        $notificationReceiver = NotificationReceiver::factory()->create();

        // 関連するエンティティが存在することを確認
        $this->assertContains($notificationReceiver->entity_type, ['App\Models\User', 'App\Models\Company', 'App\Models\Operator']);
        $this->assertNotNull($notificationReceiver->entity_id);

        $entityClass = $notificationReceiver->entity_type;
        $this->assertDatabaseHas((new $entityClass)->getTable(), [
            'id' => $notificationReceiver->entity_id,
        ]);
    }
}
