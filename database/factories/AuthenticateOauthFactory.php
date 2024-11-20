<?php

namespace Database\Factories;

use App\Models\AuthenticateOauth;
use App\Models\Site;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use App\Models\AuthProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuthenticateOauth>
 */
class AuthenticateOauthFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = AuthenticateOauth::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // ランダムにエンティティタイプを選択
        $entityTypes = [
            User::class,
            Company::class,
            Operator::class,
        ];
        $entityType = $this->faker->randomElement($entityTypes);

        return [
            'auth_code' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'site_id' => Site::factory(),
            'entity_type' => $entityType,
            'entity_id' => $entityType::factory(),
            'auth_provider_id' => AuthProvider::factory(),
            'token' => Str::random(64), // OAuthトークンを模擬
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }

    /**
     * 有効期限切れのOAuth認証情報を設定
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * 特定の認証プロバイダーのOAuth認証情報を設定
     */
    public function forProvider(AuthProvider $provider): static
    {
        return $this->state(fn (array $attributes) => [
            'auth_provider_id' => $provider->id,
        ]);
    }
}
