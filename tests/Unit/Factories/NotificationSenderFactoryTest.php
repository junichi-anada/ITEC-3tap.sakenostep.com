<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\NotificationSender;
use App\Models\Notification;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * NotificationSenderFactoryのテストクラス
 */
final class NotificationSenderFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NotificationSenderFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification_sender()
    {
        $notificationSender = NotificationSender::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('notification_senders', [
            'id' => $notificationSender->id,
            'notification_id' => $notificationSender->notification_id,
            'entity_type' => $notificationSender->entity_type,
            'entity_id' => $notificationSender->entity_id,
        ]);

        // 属性が期待通りであることを確認
        $this->assertContains($notificationSender->entity_type, ['App\Models\User', 'App\Models\Company', 'App\Models\Operator']);
        $this->assertInstanceOf(Notification::class, $notificationSender->notification);
        $this->assertNotNull($notificationSender->entity_id);
    }

    /**
     * NotificationSenderFactoryがofEntityTypeメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_sender_with_specific_entity_type()
    {
        $entityType = User::class;
        $notificationSender = NotificationSender::factory()->ofEntityType($entityType)->create();

        $this->assertEquals($entityType, $notificationSender->entity_type);
        $this->assertDatabaseHas('notification_senders', [
            'id' => $notificationSender->id,
            'entity_type' => $entityType,
        ]);
    }

    /**
     * NotificationSenderFactoryがforEntityメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_sender_for_specific_entity()
    {
        $user = User::factory()->create();
        $notificationSender = NotificationSender::factory()->forEntity($user)->create();

        $this->assertEquals(get_class($user), $notificationSender->entity_type);
        $this->assertEquals($user->id, $notificationSender->entity_id);
        $this->assertDatabaseHas('notification_senders', [
            'id' => $notificationSender->id,
            'entity_type' => get_class($user),
            'entity_id' => $user->id,
        ]);
    }

    /**
     * NotificationSenderFactoryが関連する通知を正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_notification()
    {
        $notification = Notification::factory()->create();
        $notificationSender = NotificationSender::factory()->create([
            'notification_id' => $notification->id,
        ]);

        $this->assertEquals($notification->id, $notificationSender->notification_id);
        $this->assertInstanceOf(Notification::class, $notificationSender->notification);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
        ]);
    }

    /**
     * NotificationSenderFactoryが関連するエンティティを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_entity()
    {
        $notificationSender = NotificationSender::factory()->create();

        // 関連するエンティティが存在することを確認
        $this->assertContains($notificationSender->entity_type, ['App\Models\User', 'App\Models\Company', 'App\Models\Operator']);
        $this->assertNotNull($notificationSender->entity_id);

        $entityClass = $notificationSender->entity_type;
        $this->assertDatabaseHas((new $entityClass)->getTable(), [
            'id' => $notificationSender->entity_id,
        ]);
    }

    /**
     * NotificationSenderFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_sender_with_multiple_states()
    {
        $notification = Notification::factory()->create();
        $company = Company::factory()->create();

        $notificationSender = NotificationSender::factory()
            ->forEntity($company)
            ->create([
                'notification_id' => $notification->id,
            ]);

        $this->assertEquals(Company::class, $notificationSender->entity_type);
        $this->assertEquals($company->id, $notificationSender->entity_id);
        $this->assertEquals($notification->id, $notificationSender->notification_id);
        $this->assertDatabaseHas('notification_senders', [
            'id' => $notificationSender->id,
            'entity_type' => Company::class,
            'entity_id' => $company->id,
            'notification_id' => $notification->id,
        ]);
    }
}
