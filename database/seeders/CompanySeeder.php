<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 実データを挿入
        Company::create([
            'company_code' => 'COMPANY-UUID-1234-5678',
            'company_name' => '酒のステップ',
            'name' => '沼田',
            'postal_code' => '034-0011',
            'address' => '青森県十和田市稲生町20−13',
            'phone' => '0176-23-3541',
        ]);

        // ランダムなテストデータを挿入（例: 10件）
        Company::factory()->count(10)->create();
    }
}
