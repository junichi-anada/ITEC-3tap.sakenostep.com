<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\NotificationSendMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * NotificationSendMethodFactoryのテストクラス
 */
final class NotificationSendMethodFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NotificationSendMethodFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification_send_method()
    {
        $sendMethod = NotificationSendMethod::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('notification_send_methods', [
            'id' => $sendMethod->id,
            'name' => $sendMethod->name,
            'description' => $sendMethod->description,
        ]);

        // 属性が期待通りであることを確認
        $this->assertIsString($sendMethod->name);
        $this->assertContains($sendMethod->name, ['メール', 'SMS', 'プッシュ通知', 'Webhook']);
        $this->assertIsString($sendMethod->description);
    }

    /**
     * NotificationSendMethodFactoryが一意なnameを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_send_method_name()
    {
        $sendMethod1 = NotificationSendMethod::factory()->create();
        $sendMethod2 = NotificationSendMethod::factory()->create();

        $this->assertNotEquals($sendMethod1->name, $sendMethod2->name);
    }

    /**
     * NotificationSendMethodFactoryがwithNameメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_send_method_with_specific_name()
    {
        $specificName = 'メール';
        $sendMethod = NotificationSendMethod::factory()->withName($specificName)->create();

        $this->assertEquals($specificName, $sendMethod->name);
        $this->assertDatabaseHas('notification_send_methods', [
            'id' => $sendMethod->id,
            'name' => $specificName,
        ]);
    }

    /**
     * NotificationSendMethodFactoryがwithDescriptionメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_send_method_with_specific_description()
    {
        $specificDescription = 'メールを使用��た通知';
        $sendMethod = NotificationSendMethod::factory()->withDescription($specificDescription)->create();

        $this->assertEquals($specificDescription, $sendMethod->description);
        $this->assertDatabaseHas('notification_send_methods', [
            'id' => $sendMethod->id,
            'description' => $specificDescription,
        ]);
    }

    /**
     * NotificationSendMethodFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_send_method_with_multiple_states()
    {
        $specificName = 'Webhook';
        $specificDescription = '外部システムへの通知を行います。';

        $sendMethod = NotificationSendMethod::factory()
            ->withName($specificName)
            ->withDescription($specificDescription)
            ->create();

        $this->assertEquals($specificName, $sendMethod->name);
        $this->assertEquals($specificDescription, $sendMethod->description);
        $this->assertDatabaseHas('notification_send_methods', [
            'id' => $sendMethod->id,
            'name' => $specificName,
            'description' => $specificDescription,
        ]);
    }
}
