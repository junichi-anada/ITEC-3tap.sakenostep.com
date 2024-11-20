<?php

namespace Database\Factories;

use App\Models\Authenticate;
use App\Models\Site;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Authenticate>
 */
class AuthenticateFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = Authenticate::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'auth_code' => strtoupper(Str::random(10)),
            'site_id' => Site::factory(),
            'entity_type' => $this->faker->randomElement([
                User::class,
                Company::class,
                Operator::class
            ]),
            'entity_id' => function (array $attributes) {
                return $attributes['entity_type']::factory()->create()->id;
            },
            'login_code' => $this->faker->userName,
            'password' => Hash::make('password'),
            'expires_at' => now()->addDays(30),
        ];
    }

    /**
     * 有効期限なしの状態を設定
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withoutExpiration(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => null,
            ];
        });
    }

    /**
     * 有効期限切れの状態を設定
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->subDay(),
            ];
        });
    }
}
