<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Site;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ItemFactory
 *
 * このファクトリは、商品データを生成します。
 * 主な仕様:
 * - 商品コード、サイトID、カテゴリID、メーカー名、商品名、説明、単価、単位ID、ソース情報、お気に入りフラグ、公開日時を生成します。
 * 制限事項:
 * - 存在しないサイト、カテゴリ、単位への関連付けは行いません。
 */
class ItemFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_code' => $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'site_id' => Site::factory(),
            'category_id' => ItemCategory::factory(),
            'maker_name' => $this->faker->company(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'unit_price' => $this->faker->randomFloat(2, 100, 10000),
            'unit_id' => ItemUnit::factory(),
            'from_source' => $this->faker->randomElement(['MANUAL', 'IMPORT']),
            'is_recommended' => $this->faker->boolean(20), // 20%の確率でおすすめ
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * おすすめ商品を生成
     */
    public function recommended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recommended' => true,
        ]);
    }

    /**
     * 公開済みの商品を生成
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * 非公開の商品を生成
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
