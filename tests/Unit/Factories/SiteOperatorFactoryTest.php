<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\SiteOperator;
use App\Models\Site;
use App\Models\Operator;
use App\Models\SiteOperatorRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * SiteOperatorFactoryのテストクラス
 */
final class SiteOperatorFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SiteOperatorFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_operator()
    {
        $siteOperator = SiteOperator::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('site_operator', [
            'id' => $siteOperator->id,
            'site_id' => $siteOperator->site_id,
            'operator_id' => $siteOperator->operator_id,
            'role_id' => $siteOperator->role_id,
        ]);

        // 属性が期待通りであることを確認
        $this->assertInstanceOf(Site::class, $siteOperator->site);
        $this->assertInstanceOf(Operator::class, $siteOperator->operator);
        $this->assertInstanceOf(SiteOperatorRole::class, $siteOperator->role);
    }

    /**
     * SiteOperatorFactoryが特定のロールでオペレーターを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_operator_with_specific_role()
    {
        $role = SiteOperatorRole::factory()->create(['name' => '管理者']);
        $siteOperator = SiteOperator::factory()->withRole($role)->create();

        $this->assertEquals($role->id, $siteOperator->role_id);
        $this->assertDatabaseHas('site_operator', [
            'id' => $siteOperator->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * SiteOperatorFactoryが特定のサイトでオペレーターを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_operator_for_specific_site()
    {
        $site = Site::factory()->create(['name' => 'メインサイト']);
        $siteOperator = SiteOperator::factory()->forSite($site)->create();

        $this->assertEquals($site->id, $siteOperator->site_id);
        $this->assertDatabaseHas('site_operator', [
            'id' => $siteOperator->id,
            'site_id' => $site->id,
        ]);
    }

    /**
     * SiteOperatorFactoryが特定のオペレーターでオペレーターを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_operator_for_specific_operator()
    {
        $operator = Operator::factory()->create(['name' => '山田 太郎']);
        $siteOperator = SiteOperator::factory()->forOperator($operator)->create();

        $this->assertEquals($operator->id, $siteOperator->operator_id);
        $this->assertDatabaseHas('site_operator', [
            'id' => $siteOperator->id,
            'operator_id' => $operator->id,
        ]);
    }

    /**
     * SiteOperatorFactoryが関連するサイトとオペレーターを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site_and_operator()
    {
        $site = Site::factory()->create();
        $operator = Operator::factory()->create();
        $role = SiteOperatorRole::factory()->create(['name' => '編集者']);

        $siteOperator = SiteOperator::factory()->create([
            'site_id' => $site->id,
            'operator_id' => $operator->id,
            'role_id' => $role->id,
        ]);

        $this->assertEquals($site->id, $siteOperator->site_id);
        $this->assertEquals($operator->id, $siteOperator->operator_id);
        $this->assertEquals($role->id, $siteOperator->role_id);
        $this->assertInstanceOf(Site::class, $siteOperator->site);
        $this->assertInstanceOf(Operator::class, $siteOperator->operator);
        $this->assertInstanceOf(SiteOperatorRole::class, $siteOperator->role);
        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
        ]);
        $this->assertDatabaseHas('operators', [
            'id' => $operator->id,
        ]);
        $this->assertDatabaseHas('site_operator_role', [
            'id' => $role->id,
        ]);
    }
}
