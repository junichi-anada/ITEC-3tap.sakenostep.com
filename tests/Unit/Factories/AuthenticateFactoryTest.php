<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Authenticate;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * AuthenticateFactoryのテストクラス
 */
final class AuthenticateFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * AuthenticateFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_authenticate()
    {
        $authenticate = Authenticate::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('authenticates', [
            'id' => $authenticate->id,
            'auth_code' => $authenticate->auth_code,
            'site_id' => $authenticate->site_id,
            'entity_type' => $authenticate->entity_type,
            'entity_id' => $authenticate->entity_id,
            'login_code' => $authenticate->login_code,
            // パスワードはハッシュ化されているため直接確認はしない
            'expires_at' => $authenticate->expires_at,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{10}$/', $authenticate->auth_code);
        $this->assertContains($authenticate->entity_type, [User::class, Company::class, Operator::class]);
        $this->assertNotNull($authenticate->login_code);
        $this->assertNotNull($authenticate->password);
    }

    /**
     * AuthenticateFactoryが一意なauth_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_auth_code()
    {
        $authenticate1 = Authenticate::factory()->create();
        $authenticate2 = Authenticate::factory()->create();

        $this->assertNotEquals($authenticate1->auth_code, $authenticate2->auth_code);
    }

    /**
     * AuthenticateFactoryがexpired状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_expired_authenticate()
    {
        $authenticate = Authenticate::factory()->expired()->create();

        $this->assertNotNull($authenticate->expires_at);
        $this->assertLessThanOrEqual(now(), $authenticate->expires_at);
    }

    /**
     * AuthenticateFactoryがwithoutExpiration状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_authenticate_without_expiration()
    {
        $authenticate = Authenticate::factory()
            ->withoutExpiration()
            ->create();

        $this->assertNull($authenticate->expires_at);
    }

    /**
     * AuthenticateFactoryが関連するエンティティを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_entity()
    {
        $authenticate = Authenticate::factory()->create();

        // 関連するエンティティが存在することを確認
        $this->assertTrue(
            in_array($authenticate->entity_type, [User::class, Company::class, Operator::class])
        );

        $this->assertNotNull($authenticate->entity_id);

        // 実際にエンティティが存在することを確認
        $entityType = $authenticate->entity_type;
        $tableName = (new $entityType)->getTable();

        $this->assertDatabaseHas($tableName, [
            'id' => $authenticate->entity_id,
        ]);
    }
}
