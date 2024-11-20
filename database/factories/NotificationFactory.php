<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\NotificationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NotificationFactory
 *
 * このファクトリは、通知データを生成します。
 * 主な仕様:
 * - 通知コード、カテゴリID、タイトル、内容、公開開始日時、公開終了日時を生成します。
 * 制限事項:
 * - 存在しないカテゴリへの関連付けは行いません。
 */
class NotificationFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notification_code' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'category_id' => NotificationCategory::factory(),
            'title' => $this->faker->sentence(6, true),
            'content' => $this->faker->paragraph(3, true),
            'publish_start_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'publish_end_at' => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
        ];
    }

    /**
     * タイトルに特定のキーワードを含む通知を生成
     *
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTitleKeyword(string $keyword): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->sentence(6, true) . ' ' . $keyword,
        ]);
    }

    /**
     * 確実に公開中の通知を生成
     */
    public function currentlyPublished(): static
    {
        $now = now();
        return $this->state(fn (array $attributes) => [
            'publish_start_at' => $now->subDays(1),
            'publish_end_at' => $now->addDays(30),
        ]);
    }

    /**
     * 未公開の通知を生成
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'publish_start_at' => null,
            'publish_end_at' => null,
        ]);
    }
}
