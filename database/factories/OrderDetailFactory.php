<?php

namespace Database\Factories;

use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition()
    {
        $item = Item::factory()->create(); // アイテムを生成

        return [
            'order_id' => Order::factory(),
            'item_id' => $item->id,
            'volume' => $this->faker->numberBetween(1, 10),
            'detail_code' => Str::ulid(),
            'unit_price' => $item->unit_price, // unit_price を設定
            'unit_name' => $item->unit->name ?? 'default_unit', // unit_name を設定
            'price' => $item->unit_price * $this->faker->numberBetween(1, 10), // price を計算
            'tax' => $item->unit_price * 0.1, // tax を計算
            // 他の必要なフィールドをここに追加
        ];
    }
}
