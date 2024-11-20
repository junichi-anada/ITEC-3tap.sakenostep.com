<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\NotificationCategory;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * NotificationCategoryFactoryのテストクラス
 */
final class NotificationCategoryFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NotificationCategoryFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_notification_category()
    {
        $notificationCategory = NotificationCategory::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('notification_categories', [
            'id' => $notificationCategory->id,
            'category_code' => $notificationCategory->category_code,
            'site_id' => $notificationCategory->site_id,
            'name' => $notificationCategory->name,
            'description' => $notificationCategory->description,
            'parent_id' => $notificationCategory->parent_id,
            'priority' => $notificationCategory->priority,
            'is_published' => $notificationCategory->is_published,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $notificationCategory->category_code);
        $this->assertNotEmpty($notificationCategory->name);
        $this->assertNotEmpty($notificationCategory->description);
        $this->assertIsInt($notificationCategory->priority);
        $this->assertIsBool($notificationCategory->is_published);
        if ($notificationCategory->parent_id) {
            $this->assertDatabaseHas('notification_categories', [
                'id' => $notificationCategory->parent_id,
            ]);
        }

        $this->assertInstanceOf(Site::class, $notificationCategory->site);
    }

    /**
     * NotificationCategoryFactoryが一意なcategory_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_category_code()
    {
        $notificationCategory1 = NotificationCategory::factory()->create();
        $notificationCategory2 = NotificationCategory::factory()->create();

        $this->assertNotEquals($notificationCategory1->category_code, $notificationCategory2->category_code);
    }

    /**
     * NotificationCategoryFactoryがhighPriority状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_high_priority_notification_category()
    {
        $notificationCategory = NotificationCategory::factory()->highPriority()->create();

        $this->assertEquals(1, $notificationCategory->priority);
    }

    /**
     * NotificationCategoryFactoryがpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_published_notification_category()
    {
        $notificationCategory = NotificationCategory::factory()->published()->create();

        $this->assertTrue($notificationCategory->is_published);
    }

    /**
     * NotificationCategoryFactoryがunpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unpublished_notification_category()
    {
        $notificationCategory = NotificationCategory::factory()->unpublished()->create();

        $this->assertFalse($notificationCategory->is_published);
    }

    /**
     * NotificationCategoryFactoryが親カテゴリを持つカテゴリを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_category_with_parent()
    {
        $parentCategory = NotificationCategory::factory()->create();
        $childCategory = NotificationCategory::factory()->withParent()->create([
            'parent_id' => $parentCategory->id,
        ]);

        $this->assertEquals($parentCategory->id, $childCategory->parent_id);
        $this->assertDatabaseHas('notification_categories', [
            'id' => $childCategory->parent_id,
        ]);
    }

    /**
     * NotificationCategoryFactoryが関連するサイトを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site()
    {
        $notificationCategory = NotificationCategory::factory()->create();

        // 関連するサイトが存在することを確認
        $this->assertNotNull($notificationCategory->site);
        $this->assertInstanceOf(Site::class, $notificationCategory->site);
        $this->assertDatabaseHas('sites', [
            'id' => $notificationCategory->site_id,
        ]);
    }
}
