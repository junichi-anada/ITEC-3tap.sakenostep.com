<?php

namespace Database\Factories;

use App\Models\NotificationReceiver;
use App\Models\Notification;
use App\Models\NotificationSendMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NotificationReceiverFactory
 *
 * このファクトリは、通知の受信者データを生成します。
 *
 * 主な仕様:
 * - 通知ID、エンティティタイプ、エンティティID、送信方法ID、送信日時、既読フラグ、既読日時を生成します。
 *
 * 制限事項:
 * - 存在しない通知や送信方法、エンティティへの関連付けは行いません。
 */
class NotificationReceiverFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = NotificationReceiver::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notification_id' => Notification::factory(),
            'entity_type' => $this->faker->randomElement(['App\Models\User', 'App\Models\Company', 'App\Models\Operator']),
            'entity_id' => function (array $attributes) {
                $modelClass = $attributes['entity_type'];
                return $modelClass::factory()->create()->id;
            },
            'send_method_id' => NotificationSendMethod::factory(),
            'sent_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'is_read' => $this->faker->boolean(30), // 30%の確率で既読
            'read_at' => function (array $attributes) {
                return $attributes['is_read'] ? $this->faker->dateTimeBetween($attributes['sent_at'], 'now') : null;
            },
        ];
    }

    /**
     * 既読の通知受信者を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => $this->faker->dateTimeBetween($attributes['sent_at'], 'now'),
        ]);
    }

    /**
     * 未読の通知受信者を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unread(): static
    {
        return $this->state(fn () => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * 特定の送信方法で通知受信者を生成
     *
     * @param \App\Models\NotificationSendMethod $sendMethod
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withSendMethod(NotificationSendMethod $sendMethod): static
    {
        return $this->state(fn () => [
            'send_method_id' => $sendMethod->id,
        ]);
    }
}
