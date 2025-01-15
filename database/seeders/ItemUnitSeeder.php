<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemUnitSeeder extends Seeder
{
    /**
     * 商品単位の基本データを登録
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        // 基本的な単位データ
        $units = [
            [
                'id' => 1,
                'unit_code' => 'US', // Unset(未設定)の略
                'site_id' => 1,
                'name' => '未設定',
                'priority' => 10,
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($units as $unit) {
            // 既存のレコードがあれば更新、なければ挿入
            DB::table('item_units')->updateOrInsert(
                ['id' => $unit['id']],
                $unit
            );
        }
    }
} 