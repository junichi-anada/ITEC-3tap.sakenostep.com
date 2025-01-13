<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemCategory;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['code' => '010', 'name' => '清酒'],
            ['code' => '011', 'name' => '合成清酒'],
            ['code' => '012', 'name' => '焼酎甲類'],
            ['code' => '013', 'name' => '焼酎乙類'],
            ['code' => '014', 'name' => 'みりん'],
            ['code' => '015', 'name' => 'ビール（瓶）'],
            ['code' => '016', 'name' => 'ビール（缶）'],
            ['code' => '017', 'name' => 'ビール（樽）'],
            ['code' => '018', 'name' => '果実酒'],
            ['code' => '019', 'name' => '甘味果実酒'],
            ['code' => '020', 'name' => 'ウイスキー'],
            ['code' => '021', 'name' => 'ブランデー'],
            ['code' => '023', 'name' => 'スピリッツ'],
            ['code' => '025', 'name' => 'リキュール'],
            ['code' => '027', 'name' => '雑酒'],
            ['code' => '029', 'name' => 'その他醸造'],
            ['code' => '032', 'name' => '酒類セット'],
            ['code' => '040', 'name' => '飲料'],
            ['code' => '050', 'name' => 'タバコ'],
            ['code' => '060', 'name' => '加工食品'],
            ['code' => '062', 'name' => '菓子'],
            ['code' => '064', 'name' => 'つまみ'],
            ['code' => '070', 'name' => 'ギフト券・'],
            ['code' => '074', 'name' => '空箱'],
            ['code' => '075', 'name' => '飲料ソー'],
            ['code' => '076', 'name' => '雑貨'],
            ['code' => '077', 'name' => '空瓶'],
            ['code' => '079', 'name' => '切手類'],
            ['code' => '080', 'name' => '雑貨2'],
            ['code' => '088', 'name' => '備考'],
            ['code' => '090', 'name' => '空容器P内'],
            ['code' => '098', 'name' => '塩'],
            ['code' => '099', 'name' => '米'],
            ['code' => '310', 'name' => '燃料用アルコール'],
            ['code' => '320', 'name' => '粉末酒'],
            ['code' => '998', 'name' => '全種'],
            ['code' => '999', 'name' => '容器'],
        ];

        foreach ($categories as $index => $category) {
            ItemCategory::create([
                'category_code' => $category['code'],
                'site_id' => 1,  // site_id = 1 固定
                'name' => $category['name'],
                'priority' => $index + 1,
                'is_published' => true,
            ]);
        }
    }
}
