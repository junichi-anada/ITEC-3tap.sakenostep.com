<?php

namespace Database\Factories;

use App\Models\ItemUnit;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ItemUnitFactory
 *
 * このファクトリは、商品単位データを生成します。
 * 主な仕様:
 * - 単位コード、サイトID、名前、優先度、公開フラグを生成します。
 * 制限事項:
 * - 存在しないサイトへの関連付けは行いません。
 */
class ItemUnitFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = ItemUnit::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_code' => $this->faker->unique()->regexify('[A-Z]{3}'), // 3文字のユニークな英字コード
            'site_id' => Site::factory(),
            'name' => $this->faker->randomElement(['個', 'セット', '箱', '本', '枚']), // 一般的な単位名
            'priority' => $this->faker->numberBetween(1, 10),
            'is_published' => $this->faker->boolean(90), // 90%の確率で公開
        ];
    }

    /**
     * 優先度の高い単位を生成
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 1,
        ]);
    }

    /**
     * 公開された単位を生成
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * 非公開の単位を生成
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
