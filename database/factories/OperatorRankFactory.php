<?php

namespace Database\Factories;

use App\Models\OperatorRank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * OperatorRankFactory
 *
 * このファクトリは、オペレーターのランクデータを生成します。
 *
 * 主な仕様:
 * - ランク名、優先度を生成します。
 *
 * 制限事項:
 * - 存在しないランクへの関連付けは行いません。
 */
class OperatorRankFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = OperatorRank::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(), // ランク名（例: マネージャー、スーパーバイザー）
            'priority' => $this->faker->numberBetween(1, 10), // 優先度
        ];
    }

    /**
     * 高優先度のランクを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 1,
        ]);
    }

    /**
     * 特定の名前を持つランクを生成
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
}
