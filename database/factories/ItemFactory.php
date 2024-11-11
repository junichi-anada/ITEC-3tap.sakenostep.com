<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Site;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $site = Site::factory()->create();

        $category = ItemCategory::factory()->create([
            'site_id' => $site->id,
        ]);

        $unit = ItemUnit::factory()->create([
            'site_id' => $site->id,
        ]);

        return [
            'item_code' => Str::uuid(),
            'site_id' => $site->id,
            'category_id' => $category->id,
            'maker_name' => $this->faker->company,
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'unit_price' => $this->faker->randomFloat(2, 100, 10000),
            'unit_id' => $unit->id,
            'from_source' => $this->faker->randomElement(['MANUAL', 'IMPORT']),
            'is_recommended' => $this->faker->boolean,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
