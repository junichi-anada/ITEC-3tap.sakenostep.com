<?php

namespace Database\Factories;

use App\Models\SiteAuthProvider;
use App\Models\Site;
use App\Models\AuthProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * SiteAuthProviderFactory
 *
 * このファクトリは、サイト認証プロバイダーデータを生成します。
 *
 * 主な仕様:
 * - サイトID、認証プロバイダーID、有効フラグを生成します。
 *
 * 制限事項:
 * - 存在しないサイトや認証プロバイダーへの関連付けは行いません。
 */
class SiteAuthProviderFactory extends Factory
{
    /**
     * モデルと対応するファクトリの定義
     *
     * @var string
     */
    protected $model = SiteAuthProvider::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(), // 関連するサイト
            'auth_provider_id' => AuthProvider::factory(), // 関連する認証プロバイダー
            'is_enabled' => $this->faker->boolean(80), // 80%の確率で有効化
        ];
    }

    /**
     * 特定の認証プロバイダーと関連付けられたサイト認証プロバイダーを生成
     *
     * @param \App\Models\AuthProvider $authProvider
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forAuthProvider(AuthProvider $authProvider): static
    {
        return $this->state(fn (array $attributes) => [
            'auth_provider_id' => $authProvider->id,
        ]);
    }

    /**
     * 特定のサイトと関連付けられたサイト認証プロバイダーを生成
     *
     * @param \App\Models\Site $site
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSite(Site $site): static
    {
        return $this->state(fn (array $attributes) => [
            'site_id' => $site->id,
        ]);
    }

    /**
     * 認証プロバイダーを有効にしたサイト認証プロバイダーを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => true,
        ]);
    }

    /**
     * 認証プロバイダーを無効にしたサイト認証プロバイダーを生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }
}
