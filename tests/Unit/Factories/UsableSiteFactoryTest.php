<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\UsableSite;
use App\Models\User;
use App\Models\Company;
use App\Models\Operator;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * UsableSiteFactoryのテストクラス
 */
final class UsableSiteFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UsableSiteFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_usable_site()
    {
        $usableSite = UsableSite::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('usable_sites', [
            'id' => $usableSite->id,
            'entity_type' => $usableSite->entity_type,
            'entity_id' => $usableSite->entity_id,
            'site_id' => $usableSite->site_id,
            'shared_login_allowed' => $usableSite->shared_login_allowed,
        ]);

        // 属性が期待通りであることを確認
        $this->assertContains($usableSite->entity_type, [User::class, Company::class, Operator::class]);
        $this->assertNotNull($usableSite->entity_id);
        $this->assertInstanceOf(Site::class, $usableSite->site);
        $this->assertIsBool($usableSite->shared_login_allowed);
    }

    /**
     * UsableSiteFactoryが特定のエンティティタイプでUsableSiteを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_usable_site_with_specific_entity_type()
    {
        $entityType = User::class;
        $usableSite = UsableSite::factory()->ofEntityType($entityType)->create();

        $this->assertEquals($entityType, $usableSite->entity_type);
        $this->assertDatabaseHas('usable_sites', [
            'id' => $usableSite->id,
            'entity_type' => $entityType,
        ]);
    }

    /**
     * UsableSiteFactoryが特定のエンティティに関連付けられたUsableSiteを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_usable_site_for_specific_entity()
    {
        $user = User::factory()->create();
        $usableSite = UsableSite::factory()->forEntity($user)->create();

        $this->assertEquals(get_class($user), $usableSite->entity_type);
        $this->assertEquals($user->id, $usableSite->entity_id);
        $this->assertDatabaseHas('usable_sites', [
            'id' => $usableSite->id,
            'entity_type' => get_class($user),
            'entity_id' => $user->id,
        ]);
    }

    /**
     * UsableSiteFactoryが共有ログイン許可フラグを正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_usable_site_with_shared_login_allowed()
    {
        $usableSite = UsableSite::factory()->withSharedLogin()->create();

        $this->assertTrue($usableSite->shared_login_allowed);
        $this->assertDatabaseHas('usable_sites', [
            'id' => $usableSite->id,
            'shared_login_allowed' => true,
        ]);
    }

    /**
     * UsableSiteFactoryが共有ログイン不許可フラグを正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_usable_site_without_shared_login()
    {
        $usableSite = UsableSite::factory()->withoutSharedLogin()->create();

        $this->assertFalse($usableSite->shared_login_allowed);
        $this->assertDatabaseHas('usable_sites', [
            'id' => $usableSite->id,
            'shared_login_allowed' => false,
        ]);
    }

    /**
     * UsableSiteFactoryが一意な組み合わせを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_unique_usable_site_combinations()
    {
        $usableSite1 = UsableSite::factory()->create();
        $usableSite2 = UsableSite::factory()->create();

        $this->assertNotEquals($usableSite1->entity_type, $usableSite2->entity_type);
        $this->assertNotEquals($usableSite1->entity_id, $usableSite2->entity_id);
        $this->assertNotEquals($usableSite1->site_id, $usableSite2->site_id);
    }

    /**
     * UsableSiteFactoryが関連するサイトを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site()
    {
        $usableSite = UsableSite::factory()->create();

        // 関連するサイトが存在することを確認
        $this->assertNotNull($usableSite->site);
        $this->assertInstanceOf(Site::class, $usableSite->site);
        $this->assertDatabaseHas('sites', [
            'id' => $usableSite->site_id,
        ]);
    }

    /**
     * UsableSiteFactoryが関連するエンティティを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_entity()
    {
        $usableSite = UsableSite::factory()->create();

        // 関連するエンティティが存在することを確認
        $this->assertContains($usableSite->entity_type, [User::class, Company::class, Operator::class]);
        $this->assertNotNull($usableSite->entity_id);

        $entityClass = $usableSite->entity_type;
        $this->assertDatabaseHas((new $entityClass)->getTable(), [
            'id' => $usableSite->entity_id,
        ]);
    }
}
