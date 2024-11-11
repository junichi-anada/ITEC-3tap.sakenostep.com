<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_code' => Str::ulid(), // order_code を生成
            'user_id' => User::factory(),
            'site_id' => Site::factory(),
            'ordered_at' => null, // 初期状態では注文されていない
            // 他の必要なフィールドをここに追加
        ];
    }
}
