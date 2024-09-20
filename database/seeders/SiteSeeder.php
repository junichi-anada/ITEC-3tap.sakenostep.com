<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\Company;


class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 実データのSiteを挿入
        Site::create([
            'site_code' => 'SITE-UUID-1234-5678',
            'company_id' => 1, // company_id = 1 固定
            'url' => 'https://3tap.sakenostep.itec.local',
            'name' => '酒のステップ-3TAPシステム',
            'description' => '酒のステップの3TAPシステムです。',
            'is_btob' => true,
        ]);

        // ランダムなサンプルデータを挿入（例: 10件）
        Site::factory()->count(10)->create();
    }
}
