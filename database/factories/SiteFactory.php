<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Site;
use App\Models\Company;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

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
            'site_code' => Str::uuid(),  // UUIDで生成
            'company_id' => Company::where('id', '!=', 1)->inRandomOrder()->first()->id,  // 1以外のランダムなCompanyのIDを取得
            'url' => $this->faker->url,
            'name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'is_btob' => $this->faker->boolean,
        ];
    }
}
