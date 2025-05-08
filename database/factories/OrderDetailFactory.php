<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * OrderDetailFactory
 *
 * このファクトリは、注文明細データを生成します。
 *
 * 主な仕様:
 * - 明細コード、注文ID、商品ID、数量、単価、単位名、価格、税率、処理日時を生成します。
 * - 価格は数量と単価から計算されます。
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
        $volume = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->randomFloat(2, 100, 5000); // 単価
        $price = $volume * $unitPrice; // 価格を計算
        $taxRate = $this->faker->randomElement([8, 10]); // 税率 (8% or 10%)

        // processed_at は order の ordered_at 以降にするのが自然だが、
        // ここでは単純化のため、関連する Order を参照せずに生成する
        $processedAt = $this->faker->optional(0.6)->dateTimeBetween('-6 months', 'now');

        return [
            'detail_code' => $this->faker->unique()->regexify('ODTL[0-9]{7}'), // 例: ODTL1234567
            'order_id' => Order::factory(), // 関連する注文
            'item_id' => Item::factory(),   // 関連する商品
            'volume' => $volume,            // 数量
            'unit_price' => $unitPrice,     // 単価
            'unit_name' => '個',            // 単位名 (仮。Itemから取得推奨)
            'price' => $price,              // 価格
            'tax' => $taxRate,              // 税率 (%)
            'processed_at' => $processedAt, // 処理日時（オプション）
        ];
    }

    /**
     * 処理済みの注文明細を生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function processed(): static
    {
        // OrderDetail の processed_at は Order の processed_at と連動するべきだが、
        // ここでは単純化のため、現在時刻に近い日時を設定する
        return $this->state(fn (array $attributes) => [
            'processed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
