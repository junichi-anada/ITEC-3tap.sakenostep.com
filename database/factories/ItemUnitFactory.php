<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ItemUnit;
use App\Models\Site;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemUnit>
 */
class ItemUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_code' => Str::uuid(),  // UUIDで生成
            'site_id' => Site::where('id', '!=', 1)->inRandomOrder()->first()->id,  // 1以外のランダムなSiteのIDを取得
            'name' => $this->faker->word,
            'priority' => $this->faker->numberBetween(1, 100),
            'is_published' => $this->faker->boolean,
        ];
    }
}
