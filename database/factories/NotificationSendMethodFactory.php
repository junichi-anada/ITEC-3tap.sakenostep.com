<?php

namespace Database\Factories;

use App\Models\NotificationSendMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NotificationSendMethodFactory
 *
 * このファクトリは、通知送信方法データを生成します。
 *
 * 主な仕様:
 * - 送信方法名、説明を生成します。
 *
 * 制限事項:
 * - 存在しない送信方法への関連付けは行いません。
 */
class NotificationSendMethodFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = NotificationSendMethod::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['メール', 'SMS', 'プッシュ通知', 'Webhook']),
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * 特定の送信方法名を持つ送信方法を生成
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
     * 詳細な説明を持つ送信方法を生成
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
