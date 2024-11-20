<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\ItemUnit;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * ItemUnitFactoryのテストクラス
 */
final class ItemUnitFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ItemUnitFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_item_unit()
    {
        $itemUnit = ItemUnit::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('item_units', [
            'id' => $itemUnit->id,
            'unit_code' => $itemUnit->unit_code,
            'site_id' => $itemUnit->site_id,
            'name' => $itemUnit->name,
            'priority' => $itemUnit->priority,
            'is_published' => $itemUnit->is_published,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z]{3}$/', $itemUnit->unit_code);
        $this->assertContains($itemUnit->name, ['��', 'セット', '箱', '本', '枚']);
        $this->assertIsInt($itemUnit->priority);
        $this->assertIsBool($itemUnit->is_published);
    }

    /**
     * ItemUnitFactoryが一意なunit_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_unit_code()
    {
        $itemUnit1 = ItemUnit::factory()->create();
        $itemUnit2 = ItemUnit::factory()->create();

        $this->assertNotEquals($itemUnit1->unit_code, $itemUnit2->unit_code);
    }

    /**
     * ItemUnitFactoryがhighPriority状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_high_priority_item_unit()
    {
        $itemUnit = ItemUnit::factory()->highPriority()->create();

        $this->assertEquals(1, $itemUnit->priority);
    }

    /**
     * ItemUnitFactoryがpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_published_item_unit()
    {
        $itemUnit = ItemUnit::factory()->published()->create();

        $this->assertTrue($itemUnit->is_published);
    }

    /**
     * ItemUnitFactoryがunpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unpublished_item_unit()
    {
        $itemUnit = ItemUnit::factory()->unpublished()->create();

        $this->assertFalse($itemUnit->is_published);
    }

    /**
     * ItemUnitFactoryが関連するサイトを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site()
    {
        $itemUnit = ItemUnit::factory()->create();

        // 関連するサイトが存在することを確認
        $this->assertNotNull($itemUnit->site);
        $this->assertInstanceOf(Site::class, $itemUnit->site);
        $this->assertDatabaseHas('sites', [
            'id' => $itemUnit->site_id,
        ]);
    }
}
