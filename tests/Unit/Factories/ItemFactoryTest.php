<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Site;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * ItemFactoryのテストクラス
 */
final class ItemFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ItemFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_item()
    {
        $item = Item::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'item_code' => $item->item_code,
            'site_id' => $item->site_id,
            'category_id' => $item->category_id,
            'maker_name' => $item->maker_name,
            'name' => $item->name,
            'description' => $item->description,
            'unit_price' => $item->unit_price,
            'unit_id' => $item->unit_id,
            'from_source' => $item->from_source,
            'is_recommended' => $item->is_recommended,
            'published_at' => $item->published_at,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $item->item_code);
        $this->assertInstanceOf(Site::class, $item->site);
        $this->assertInstanceOf(ItemCategory::class, $item->category);
        $this->assertInstanceOf(ItemUnit::class, $item->unit);
        $this->assertContains($item->from_source, ['Internal', 'External']);
        $this->assertIsBool($item->is_recommended);
        if ($item->published_at) {
            $this->assertTrue($item->published_at->isPast() || $item->published_at->isFuture());
        }
    }

    /**
     * ItemFactoryが一意なitem_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_item_code()
    {
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $this->assertNotEquals($item1->item_code, $item2->item_code);
    }

    /**
     * ItemFactoryがrecommended状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_recommended_item()
    {
        $item = Item::factory()->recommended()->create();

        $this->assertTrue($item->is_recommended);
    }

    /**
     * ItemFactoryがpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_published_item()
    {
        $item = Item::factory()->published()->create();

        $this->assertNotNull($item->published_at);
        $this->assertTrue($item->published_at->isPast());
    }

    /**
     * ItemFactoryがunpublished状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unpublished_item()
    {
        $item = Item::factory()->unpublished()->create();

        $this->assertNull($item->published_at);
    }

    /**
     * ItemFactoryが関連するモデルを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_models()
    {
        $item = Item::factory()->create();

        // 関連するモデルが存在することを確認
        $this->assertInstanceOf(Site::class, $item->site);
        $this->assertInstanceOf(ItemCategory::class, $item->category);
        $this->assertInstanceOf(ItemUnit::class, $item->unit);

        // 関連するテーブルにレコードが存在することを確認
        $this->assertDatabaseHas('sites', [
            'id' => $item->site_id,
        ]);
        $this->assertDatabaseHas('item_categories', [
            'id' => $item->category_id,
        ]);
        $this->assertDatabaseHas('item_units', [
            'id' => $item->unit_id,
        ]);
    }

    /**
     * ItemFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_item_with_multiple_states()
    {
        $item = Item::factory()
            ->recommended()
            ->published()
            ->create();

        $this->assertTrue($item->is_recommended);
        $this->assertNotNull($item->published_at);
        $this->assertTrue($item->published_at->isPast());
    }
}
