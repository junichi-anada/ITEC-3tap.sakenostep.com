<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operator>
 */
class OperatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Companyファクトリを使用して関連付けられたCompanyを生成
        $company = Company::factory()->create();

        return [
            'operator_code' => $this->faker->uuid(),  // UUIDで生成
            'company_id' => Company::where('id', '!=', 1)->inRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'operator_rank_id' => 1,  // 1: 管理者
        ];
    }
}
