<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 他のシーダークラスを呼び出す
        $this->call([
            CompanySeeder::class,
            SiteSeeder::class,
            OperatorRankSeeder::class,
            OperatorSeeder::class,
            UserSeeder::class,
            AuthenticateSeeder::class,
            ItemSeeder::class,
        ]);
    }
}
