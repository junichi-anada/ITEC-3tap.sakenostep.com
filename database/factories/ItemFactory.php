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
        // 先に ItemCategory と ItemUnit を生成
        $category = ItemCategory::factory()->create([
            'site_id' => Site::where('id', '!=', 1)->inRandomOrder()->first()->id,  // site_id = 1以外のランダムなCategoryのIDを取得
        ]);

        $unit = ItemUnit::factory()->create([
            'site_id' => Site::where('id', '!=', 1)->inRandomOrder()->first()->id,  // site_id = 1以外のランダムなUnitのIDを取得
        ]);

        return [
            'item_code' => Str::uuid(),  // UUIDで生成
            'site_id' => $category->site_id,  // ItemCategory と同じ Site を使用
            'category_id' => $category->id,
            'maker_name' => $this->faker->company,
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'unit_price' => $this->faker->randomFloat(2, 100, 10000),  // 100から10000の範囲でランダム
            'unit_id' => $unit->id,
            'from_source' => $this->faker->randomElement(['MANUAL', 'IMPORT']),
            'is_recommended' => $this->faker->boolean,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
