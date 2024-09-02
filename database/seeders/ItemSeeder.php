<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Site;
use App\Models\ItemCategory;
use App\Models\ItemUnit;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. 実データのItemCategoryを挿入
        $category = ItemCategory::create([
            'category_code' => 'CAT-UUID-1234',
            'site_id' => 1,  // site_id = 1 固定
            'name' => 'ビール',
            'priority' => 1,
            'is_published' => true,
        ]);

        ItemCategory::create([
            'category_code' => 'CAT-UUID-4321',
            'site_id' => 1,  // site_id = 1 固定
            'name' => '日本酒',
            'priority' => 2,
            'is_published' => true,
        ]);

        ItemCategory::create([
            'category_code' => 'CAT-UUID-4231',
            'site_id' => 1,  // site_id = 1 固定
            'name' => '焼酎',
            'priority' => 3,
            'is_published' => true,
        ]);

        // 2. 実データのItemUnitを挿入
        ItemUnit::create([
            'unit_code' => 'UNIT-UUID-5678',
            'site_id' => $category->site_id,  // 同じSiteを使う
            'name' => '缶',
            'priority' => 1,
            'is_published' => true,
        ]);

        ItemUnit::create([
            'unit_code' => 'UNIT-UUID-1234',
            'site_id' => $category->site_id,  // 同じSiteを使う
            'name' => '本',
            'priority' => 1,
            'is_published' => true,
        ]);


        // 3. 実データItemを挿入
        Item::create([
            'item_code' => 'ITEM-UUID-1234-5678',
            'site_id' => 1,  // site_id = 1 固定
            'category_id' => 1,  // category_id = 1 固定
            'maker_name' => 'サッポロビール',
            'name' => 'サッポロ生ビール黒ラベル',
            'description' => 'サッポロ生ビール黒ラベル 350ml缶',
            'unit_price' => 5000.00,
            'unit_id' => 1,  //  unit_id = 1 固定
            'from_source' => 'MANUAL',
            'is_recommended' => true,
            'published_at' => now(),
        ]);

        Item::create([
            'item_code' => 'ITEM-UUID-5678-1234',
            'site_id' => 1,  // site_id = 1 固定
            'category_id' => 1,  // category_id = 1 固定
            'maker_name' => 'アサヒビール',
            'name' => 'スーパードライ',
            'description' => 'アサヒスーパードライ 350ml缶',
            'unit_price' => 5000.00,
            'unit_id' => 1,  //  unit_id = 1 固定
            'from_source' => 'MANUAL',
            'is_recommended' => true,
            'published_at' => now(),
        ]);

        Item::create([
            'item_code' => 'ITEM-UUID-5634-1278',
            'site_id' => 1,  // site_id = 1 固定
            'category_id' => 2,  // category_id = 1 固定
            'maker_name' => '西田酒造',
            'name' => '田酒 純米大吟醸720ml',
            'description' => '気品のある円やかなふくらみ、厚みがありながらキレの良さが際立つ手造りの銘品。',
            'unit_price' => 5000.00,
            'unit_id' => 1,  //  unit_id = 1 固定
            'from_source' => 'MANUAL',
            'is_recommended' => true,
            'published_at' => now(),
        ]);

        // 4. ランダムなサンプルデータを挿入（例: 10件）
        Item::factory()->count(10)->create();
    }
}
