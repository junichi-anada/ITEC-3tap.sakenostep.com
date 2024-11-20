<?php

namespace Database\Factories;

use App\Models\NotificationSender;
use App\Models\Notification;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NotificationSenderFactory
 *
 * このファクトリは、通知送信者データを生成します。
 *
 * 主な仕様:
 * - 通知ID、エンティティタイプ、エンティティIDを生成します。
 *
 * 制限事項:
 * - 存在しない通知やエンティティへの関連付けは行いません。
 */
class NotificationSenderFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = NotificationSender::class;

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
        ];
    }

    /**
     * 特定のエンティティタイプで送信者を生成
     *
     * @param string $entityType
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function ofEntityType(string $entityType): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_type' => $entityType,
            'entity_id' => (new $entityType)->factory()->create()->id,
        ]);
    }

    /**
     * 特定のエンティティに関連付けられた送信者を生成
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forEntity($entity): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
        ]);
    }
}
