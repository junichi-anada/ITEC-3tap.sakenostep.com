<?php

namespace Database\Factories;

use App\Models\Authenticate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuthenticateFactory extends Factory
{
    protected $model = Authenticate::class;

    public function definition()
    {
        return [
            'auth_code' => Str::uuid(),
            'site_id' => 1, // 適切なサイトIDを設定
            'entity_type' => 'App\Models\User',
            'entity_id' => 1, // 適切なユーザーIDを設定
            'login_code' => $this->faker->unique()->userName,
            'password' => bcrypt('password'), // 適切なパスワードを設定
            'expires_at' => now()->addDays(365),
        ];
    }
}
