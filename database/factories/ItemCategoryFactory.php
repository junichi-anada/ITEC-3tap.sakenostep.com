<?php

namespace Database\Factories;

use App\Models\ItemCategory;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ItemCategoryFactory
 *
 * このファクトリは、商品カテゴリデータを生成します。
 * 主な仕様:
 * - カテゴリコード、サイトID、名前、優先度、公開フラグを生成します。
 * 制限事項:
 * - 存在しないサイトへの関連付けは行いません。
 */
class ItemCategoryFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = ItemCategory::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_code' => $this->faker->unique()->regexify('[A-Z0-9]{6}'),
            'site_id' => Site::factory(),
            'name' => $this->faker->word(),
            'priority' => $this->faker->numberBetween(1, 10),
            'is_published' => $this->faker->boolean(80), // 80%の確率で公開
        ];
    }

    /**
     * 優先度の高いカテゴリを生成
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 1,
        ]);
    }

    /**
     * 公開されたカテゴリを生成
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * 非公開のカテゴリを生成
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
