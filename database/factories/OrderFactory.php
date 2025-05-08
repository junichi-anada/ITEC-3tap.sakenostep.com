<?php

namespace Database\Factories;

use App\Models\Customer; // User を Customer に変更
use App\Models\Order;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * OrderFactory
 *
 * このファクトリは、注文データを生成します。
 *
 * 主な仕様:
 * - 注文コード、サイトID、ユーザーID、合計金額、税金、送料、注文日時、処理日時、出荷日時を生成します。
 *
 * 制限事項:
 * - 存在しないサイトやユーザーへの関連付けは行いません。
 */
class OrderFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderedAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $processedAt = $this->faker->optional(0.7)->dateTimeBetween($orderedAt, 'now');
        $exportedAt = $this->faker->optional(0.5)->dateTimeBetween($processedAt ?? $orderedAt, 'now');

        return [
            'order_code' => $this->faker->unique()->regexify('ORD[0-9]{6}'), // 例: ORD123456
            'site_id' => Site::factory(), // 関連するサイト
            'user_id' => Customer::factory(), // 注文したユーザー (Customer)
            'total_price' => $this->faker->randomFloat(2, 1000, 50000), // 合計金額
            'tax' => $this->faker->randomFloat(2, 50, 5000), // 税金
            'postage' => $this->faker->randomFloat(2, 100, 2000), // 送料
            'ordered_at' => $orderedAt, // 注文日時
            'processed_at' => $processedAt, // 処理日時（オプション）
            'exported_at' => $exportedAt, // 出荷日時（オプション）
        ];
    }

    /**
     * 処理済みの注文を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => $this->faker->dateTimeBetween($attributes['ordered_at'], 'now'),
        ]);
    }

    /**
     * 出荷済みの注文を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function exported(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => $attributes['processed_at'] ?? $this->faker->dateTimeBetween($attributes['ordered_at'], 'now'),
            'exported_at' => $this->faker->dateTimeBetween($attributes['processed_at'] ?? $attributes['ordered_at'], 'now'),
        ]);
    }

    /**
     * 注文がキャンセルされた状態を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => null,
            'exported_at' => null,
            'total_price' => 0,
            'tax' => 0,
            'postage' => 0,
        ]);
    }
}
