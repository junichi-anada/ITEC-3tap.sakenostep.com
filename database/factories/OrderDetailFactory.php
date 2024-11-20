<?php

namespace Database\Factories;

use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * OrderDetailFactory
 *
 * このファクトリは、注文詳細データを生成します。
 *
 * 主な仕様:
 * - 詳細コード、注文ID、商品ID、数量、単価、単位名、価格、税金、処理日時を生成します。
 *
 * 制限事項:
 * - 存在しない注文や商品への関連付けは行いません。
 */
class OrderDetailFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = OrderDetail::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'detail_code' => $this->faker->unique()->regexify('DET[0-9]{6}'), // 例: DET123456
            'order_id' => Order::factory(), // 関連する注文
            'item_id' => Item::factory(), // 注文された商品
            'volume' => $this->faker->numberBetween(1, 100), // 数量
            'unit_price' => $this->faker->randomFloat(2, 100, 10000), // 単価
            'unit_name' => $this->faker->randomElement(['個', 'セット', '箱', '本', '枚']), // 単位名
            'price' => function ($attributes) {
                return $attributes['volume'] * $attributes['unit_price'];
            }, // 価格（数量 × 単価）
            'tax' => function ($attributes) {
                return round($attributes['price'] * 0.1, 2); // 税金（価格の10%）
            },
            'processed_at' => $this->faker->optional(0.8)->dateTimeBetween($this->faker->dateTimeBetween('-1 year', 'now'), 'now'),
        ];
    }

    /**
     * 処理済みの注文詳細を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => $this->faker->dateTimeBetween($attributes['ordered_at'] ?? '-1 month', 'now'),
        ]);
    }

    /**
     * 特定の注文に関連付けられた注文詳細を生成
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forOrder(Order $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $order->id,
        ]);
    }

    /**
     * 特定の商品に関連付けられた注文詳細を生成
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forItem(Item $item): static
    {
        return $this->state(fn (array $attributes) => [
            'item_id' => $item->id,
            'unit_price' => $item->unit_price,
            'unit_name' => $item->unit->name,
            'price' => $attributes['volume'] * $item->unit_price,
            'tax' => round(($attributes['volume'] * $item->unit_price) * 0.1, 2),
        ]);
    }
}
