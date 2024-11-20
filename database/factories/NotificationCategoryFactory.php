<?php

namespace Database\Factories;

use App\Models\NotificationCategory;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NotificationCategoryFactory
 *
 * このファクトリは、通知カテゴリデータを生成します。
 *
 * 主な仕様:
 * - カテゴリコード、サイトID、名前、優先度、公開フラグを生成します。
 *
 * 制限事項:
 * - 存在しないサイトへの関連付けは行いません。
 */
class NotificationCategoryFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = NotificationCategory::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(), // カテゴリ名
            'description' => $this->faker->sentence(), // カテゴリの説明
            'parent_id' => null, // 親カテゴリがない場合
            'priority' => $this->faker->numberBetween(1, 10), // 優先度
            'is_published' => $this->faker->boolean(80), // 80%の確率で公開
            'site_id' => Site::factory(), // 関連するサイト
            'category_code' => $this->faker->unique()->regexify('[A-Z0-9]{6}'), // 6文字のユニークな英数字コード
        ];
    }

    /**
     * 優先度の高いカテゴリを生成
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
     * 公開されたカテゴリを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * 非公開のカテゴリを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * 親カテゴリを持つカテゴリを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => NotificationCategory::factory(),
        ]);
    }
}
