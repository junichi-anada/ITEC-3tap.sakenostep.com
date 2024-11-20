<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Notification;
use App\Models\NotificationCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * NotificationFactoryのテストクラス
 */
final class NotificationFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NotificationFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification()
    {
        $notification = Notification::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'notification_code' => $notification->notification_code,
            'category_id' => $notification->category_id,
            'title' => $notification->title,
            'content' => $notification->content,
            'publish_start_at' => $notification->publish_start_at,
            'publish_end_at' => $notification->publish_end_at,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{10}$/', $notification->notification_code);
        $this->assertInstanceOf(NotificationCategory::class, $notification->category);
        $this->assertIsString($notification->title);
        $this->assertIsString($notification->content);
        $this->assertInstanceOf(\DateTime::class, $notification->publish_start_at);
        if ($notification->publish_end_at) {
            $this->assertInstanceOf(\DateTime::class, $notification->publish_end_at);
        }
    }

    /**
     * NotificationFactoryが一意なnotification_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_notification_code()
    {
        $notification1 = Notification::factory()->create();
        $notification2 = Notification::factory()->create();

        $this->assertNotEquals($notification1->notification_code, $notification2->notification_code);
    }

    /**
     * NotificationFactoryがwithTitleKeywordメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification_with_title_keyword()
    {
        $keyword = '重要';
        $notification = Notification::factory()->withTitleKeyword($keyword)->create();

        $this->assertStringContainsString($keyword, $notification->title);
    }

    /**
     * NotificationFactoryがcurrentlyPublishedメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_currently_published_notification()
    {
        $notification = Notification::factory()->currentlyPublished()->create();

        $this->assertLessThanOrEqual(now()->timestamp, $notification->publish_start_at->timestamp);
        $this->assertGreaterThanOrEqual(now()->timestamp, $notification->publish_end_at->timestamp);
    }

    /**
     * NotificationFactoryがunpublishedメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unpublished_notification()
    {
        $notification = Notification::factory()->unpublished()->create();

        $this->assertNull($notification->publish_start_at);
        $this->assertNull($notification->publish_end_at);
    }

    /**
     * NotificationFactoryが関連するカテゴリを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_notification_category()
    {
        $notification = Notification::factory()->create();

        // 関連するカテゴリが存在することを確認
        $this->assertNotNull($notification->category);
        $this->assertInstanceOf(NotificationCategory::class, $notification->category);
        $this->assertDatabaseHas('notification_categories', [
            'id' => $notification->category_id,
        ]);
    }
}
