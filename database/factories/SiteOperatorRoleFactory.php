<?php

namespace Database\Factories;

use App\Models\SiteOperatorRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * SiteOperatorRoleFactory
 *
 * このファクトリは、サイトオペレーターのロールデータを生成します。
 *
 * 主な仕様:
 * - ロール名、説明を生成します。
 *
 * 制限事項:
 * - 存在しないサイトオペレーターへの関連付けは行いません。
 */
class SiteOperatorRoleFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = SiteOperatorRole::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(), // ロール名（例: 管理者、編集者）
            'description' => $this->faker->sentence(), // ロールの説明
        ];
    }

    /**
     * 特定の名前を持つロールを生成
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

    /**
     * 特定の説明を持つロールを生成
     *
     * @param string $description
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withDescription(string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }
}
