<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OperatorRank;

class OperatorRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //実データの挿入
        OperatorRank::create([
            'name' => 'サイト管理者',
            'priority' => 1,
        ]);
    }
}
