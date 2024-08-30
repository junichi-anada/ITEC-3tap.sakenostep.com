<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ItemCategory;
use App\Models\Site;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemCategory>
 */
class ItemCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_code' => Str::uuid(),
            'site_id' => Site::where('id', '!=', 1)->inRandomOrder()->first()->id,  // 1以外のランダムなSiteのIDを取得
            'name' => $this->faker->word,
            'priority' => $this->faker->numberBetween(1, 100),
            'is_published' => $this->faker->boolean,
        ];
    }
}
