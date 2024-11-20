<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\AuthProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * AuthProviderFactoryのテストクラス
 */
final class AuthProviderFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * AuthProviderFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_auth_provider()
    {
        $authProvider = AuthProvider::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('auth_providers', [
            'id' => $authProvider->id,
            'provider_code' => $authProvider->provider_code,
            'name' => $authProvider->name,
            'description' => $authProvider->description,
            'is_enable' => $authProvider->is_enable,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $authProvider->provider_code);
        $this->assertContains($authProvider->name, ['Google', 'Facebook', 'Twitter', 'Line', 'GitHub']);
        $this->assertIsBool($authProvider->is_enable);
    }

    /**
     * AuthProviderFactoryが一意なprovider_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_provider_code()
    {
        $authProvider1 = AuthProvider::factory()->create();
        $authProvider2 = AuthProvider::factory()->create();

        $this->assertNotEquals($authProvider1->provider_code, $authProvider2->provider_code);
    }
}
