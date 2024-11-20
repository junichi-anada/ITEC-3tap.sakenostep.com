<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\AuthenticateOauth;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use App\Models\Site;
use App\Models\AuthProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

/**
 * AuthenticateOauthFactoryのテストクラス
 */
final class AuthenticateOauthFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * AuthenticateOauthFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_authenticate_oauth()
    {
        $authenticateOauth = AuthenticateOauth::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('authenticate_oauths', [
            'id' => $authenticateOauth->id,
            'auth_code' => $authenticateOauth->auth_code,
            'site_id' => $authenticateOauth->site_id,
            'entity_type' => $authenticateOauth->entity_type,
            'entity_id' => $authenticateOauth->entity_id,
            'auth_provider_id' => $authenticateOauth->auth_provider_id,
            'token' => $authenticateOauth->token,
            'expires_at' => $authenticateOauth->expires_at,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{10}$/', $authenticateOauth->auth_code);
        $this->assertContains($authenticateOauth->entity_type, [User::class, Company::class, Operator::class]);
        $this->assertNotNull($authenticateOauth->token);
        $this->assertEquals(64, strlen($authenticateOauth->token));
        if ($authenticateOauth->expires_at) {
            $this->assertTrue(Carbon::instance($authenticateOauth->expires_at)->isFuture());
        }
    }

    /**
     * AuthenticateOauthFactoryが一意なauth_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_auth_code()
    {
        $authenticateOauth1 = AuthenticateOauth::factory()->create();
        $authenticateOauth2 = AuthenticateOauth::factory()->create();

        $this->assertNotEquals($authenticateOauth1->auth_code, $authenticateOauth2->auth_code);
    }

    /**
     * AuthenticateOauthFactoryがexpired状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_expired_authenticate_oauth()
    {
        $authenticateOauth = AuthenticateOauth::factory()->expired()->create();

        $this->assertNotNull($authenticateOauth->expires_at);
        $this->assertTrue(Carbon::instance($authenticateOauth->expires_at)->isPast());
    }

    /**
     * AuthenticateOauthFactoryがforProviderメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_authenticate_oauth_for_specific_provider()
    {
        $provider = AuthProvider::factory()->create(['name' => 'GitHub']);

        $authenticateOauth = AuthenticateOauth::factory()->forProvider($provider)->create();

        $this->assertEquals($provider->id, $authenticateOauth->auth_provider_id);
    }

    /**
     * AuthenticateOauthFactoryが関連するエンティティを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_entity()
    {
        $authenticateOauth = AuthenticateOauth::factory()->create();

        // 関連するエンティティが存在することを確認
        $this->assertContains($authenticateOauth->entity_type, [User::class, Company::class, Operator::class]);
        $this->assertNotNull($authenticateOauth->entity_id);

        // 実際にエンティティが存在することを確認
        $entityClass = $authenticateOauth->entity_type;
        $this->assertDatabaseHas((new $entityClass)->getTable(), [
            'id' => $authenticateOauth->entity_id,
        ]);
    }
}
