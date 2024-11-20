<?php

namespace Database\Factories;

use App\Models\AuthProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuthProvider>
 */
class AuthProviderFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = AuthProvider::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_code' => $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'name' => $this->faker->randomElement(['Google', 'Facebook', 'Twitter', 'Line', 'GitHub']),
            'description' => $this->faker->sentence(),
            'is_enable' => $this->faker->boolean(80), // 80%の確率でtrue
        ];
    }
}
