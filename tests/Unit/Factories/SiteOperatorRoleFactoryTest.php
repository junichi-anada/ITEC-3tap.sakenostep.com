<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\SiteOperatorRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * SiteOperatorRoleFactoryのテストクラス
 */
final class SiteOperatorRoleFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SiteOperatorRoleFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_operator_role()
    {
        $role = SiteOperatorRole::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('site_operator_roles', [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
        ]);

        // 属性が期待通りであることを確認
        $this->assertIsString($role->name);
        $this->assertNotEmpty($role->name);
        $this->assertIsString($role->description);
        $this->assertNotEmpty($role->description);
    }

    /**
     * SiteOperatorRoleFactoryが一意なnameを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_role_name()
    {
        $role1 = SiteOperatorRole::factory()->create(['name' => '管理者']);
        $role2 = SiteOperatorRole::factory()->create(['name' => '編集者']);
        $role3 = SiteOperatorRole::factory()->create(['name' => '閲覧者']);

        $this->assertNotEquals($role1->name, $role2->name);
        $this->assertNotEquals($role1->name, $role3->name);
        $this->assertNotEquals($role2->name, $role3->name);
    }

    /**
     * SiteOperatorRoleFactoryがwithNameメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_role_with_specific_name()
    {
        $specificName = 'スーパーバイザー';
        $role = SiteOperatorRole::factory()->withName($specificName)->create();

        $this->assertEquals($specificName, $role->name);
        $this->assertDatabaseHas('site_operator_roles', [
            'id' => $role->id,
            'name' => $specificName,
        ]);
    }

    /**
     * SiteOperatorRoleFactoryがwithDescriptionメソッドを正しく適用することをテストします。
     *
     * @return void
     */
    public function test_it_creates_role_with_specific_description()
    {
        $specificDescription = 'システム全体の管理を行います。';
        $role = SiteOperatorRole::factory()->withDescription($specificDescription)->create();

        $this->assertEquals($specificDescription, $role->description);
        $this->assertDatabaseHas('site_operator_roles', [
            'id' => $role->id,
            'description' => $specificDescription,
        ]);
    }

    /**
     * SiteOperatorRoleFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_role_with_multiple_states()
    {
        $specificName = 'テスト管理者';
        $specificDescription = 'テスト環境の管理を行います。';

        $role = SiteOperatorRole::factory()
            ->withName($specificName)
            ->withDescription($specificDescription)
            ->create();

        $this->assertEquals($specificName, $role->name);
        $this->assertEquals($specificDescription, $role->description);
        $this->assertDatabaseHas('site_operator_roles', [
            'id' => $role->id,
            'name' => $specificName,
            'description' => $specificDescription,
        ]);
    }

    /**
     * SiteOperatorRoleFactoryが関連するモデルを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_models()
    {
        $role = SiteOperatorRole::factory()->create();

        // 追加の関連モデルが存在する場合はここにテストを追加
        // 例: もしSiteOperatorRoleが他のモデルに関連している場合
    }
}
