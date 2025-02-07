<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 実データの挿入
        User::create([
            'user_code' => '99001',
            'site_id' => 1,  // site_id = 1 固定
            'name' => 'Itec Anada',
            'postal_code' => '020-0838',
            'address' => '岩手県盛岡市津志田中央2丁目8-31',
            'phone' => '080-1062-9982',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'user_code' => '99002',
            'site_id' => 1,  // site_id = 1 固定
            'name' => 'Itec User',
            'postal_code' => '020-0000',
            'address' => '青森県八戸市市川町1-1-1',
            'phone' => '090-0000-0000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'user_code' => '99003',
            'site_id' => 1,  // site_id = 1 固定
            'name' => 'Step User',
            'postal_code' => '020-0000',
            'address' => '青森県十和田市稲生町1-1-1',
            'phone' => '080-0000-0000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ランダムなサンプルデータを挿入（例: 10件）
        // User::factory()->count(10)->create();
    }
}
