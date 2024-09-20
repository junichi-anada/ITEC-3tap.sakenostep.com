<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Operator;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //実データの挿入
        Operator::create([
            'operator_code' => 'OPERATOR-UUID-1234-5678',
            'company_id' => 1,  // company_id = 1 固定
            'name' => 'テスト管理者',
            'operator_rank_id' => 1,
        ]);
    }
}
