<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\ItemCategory;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * ItemCategoryFactoryのテストクラス
 */
final class ItemCategoryFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ItemCategoryFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_item_category()
    {
        $itemCategory = ItemCategory::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('item_categories', [
            'id' => $itemCategory->id,
            'category_code' => $itemCategory->category_code,
            'site_id' => $itemCategory->site_id,
            'name' => $itemCategory->name,
            'priority' => $itemCategory->priority,
            'is_published' => $itemCategory->is_published,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $itemCategory->category_code);
        $this->assertNotEmpty($itemCategory->name);
        $this->assertIsInt($itemCategory->priority);
        $this->assertIsBool($itemCategory->is_published);
    }

    /**
     * ItemCategoryFactoryが一意なcategory_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_category_code()
    {
        $itemCategory1 = ItemCategory::factory()->create();
        $itemCategory2 = ItemCategory::factory()->create();

        $this->assertNotEquals($itemCategory1->category_code, $itemCategory2->category_code);
    }

    /**
     * ItemCategoryFactoryがhighPriority状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_high_priority_item_category()
    {
        $itemCategory = ItemCategory::factory()->highPriority()->create();

        $this->assertEquals(1, $itemCategory->priority);
    }

    /**
     * ItemCategoryFactoryがpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_published_item_category()
    {
        $itemCategory = ItemCategory::factory()->published()->create();

        $this->assertTrue($itemCategory->is_published);
    }

    /**
     * ItemCategoryFactoryがunpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unpublished_item_category()
    {
        $itemCategory = ItemCategory::factory()->unpublished()->create();

        $this->assertFalse($itemCategory->is_published);
    }

    /**
     * ItemCategoryFactoryが関連するサイトを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site()
    {
        $itemCategory = ItemCategory::factory()->create();

        // 関連するサイトが存在することを確認
        $this->assertNotNull($itemCategory->site);
        $this->assertInstanceOf(Site::class, $itemCategory->site);
        $this->assertDatabaseHas('sites', [
            'id' => $itemCategory->site_id,
        ]);
    }
}
