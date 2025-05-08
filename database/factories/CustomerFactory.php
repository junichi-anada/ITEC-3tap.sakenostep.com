<?php

namespace Database\Factories;

use App\Models\Site; // Siteモデルをインポート
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Str ファサードをインポート

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_code' => Str::random(10), // 一意である必要があるため、テストごとに調整が必要な場合あり
            'site_id' => Site::factory(), // 関連するSiteも作成
            'name' => fake()->name(),
            'postal_code' => fake()->numerify('###-####'), // 日本の郵便番号形式
            'address' => fake()->address(), // Fakerの住所
            'phone' => fake()->numerify('0##-####-####'), // 日本の電話番号形式
            'phone2' => fake()->optional()->numerify('0##-####-####'), // オプショナル
            'fax' => fake()->optional()->numerify('0##-####-####'), // オプショナル
            // created_at, updated_at は自動設定される
            // deleted_at は通常null
        ];
    }
}
