<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Site;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * SiteFactoryのテストクラス
 */
final class SiteFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SiteFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site()
    {
        $site = Site::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'site_code' => $site->site_code,
            'company_id' => $site->company_id,
            'url' => $site->url,
            'name' => $site->name,
            'description' => $site->description,
            'is_btob' => $site->is_btob,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $site->site_code);
        $this->assertMatchesRegularExpression('/^(https?:\/\/)?([\w\-])+\.{1}([a-zA-Z]{2,63})([\/\w\-]*)*\/?$/', $site->url);
        $this->assertNotEmpty($site->name);
        $this->assertNotEmpty($site->description);
        $this->assertIsBool($site->is_btob);
    }

    /**
     * SiteFactoryが一意なsite_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_site_code()
    {
        $site1 = Site::factory()->create();
        $site2 = Site::factory()->create();

        $this->assertNotEquals($site1->site_code, $site2->site_code);
    }

    /**
     * SiteFactoryが一意なurlを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_url()
    {
        $site1 = Site::factory()->create();
        $site2 = Site::factory()->create();

        $this->assertNotEquals($site1->url, $site2->url);
    }

    /**
     * SiteFactoryがb2b状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_b2b_site()
    {
        $site = Site::factory()->b2b()->create();

        $this->assertTrue($site->is_btob);
        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'is_btob' => true,
        ]);
    }

    /**
     * SiteFactoryがb2c状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_b2c_site()
    {
        $site = Site::factory()->b2c()->create();

        $this->assertFalse($site->is_btob);
        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'is_btob' => false,
        ]);
    }

    /**
     * SiteFactoryが関連する会社を正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_company()
    {
        $site = Site::factory()->create();

        // 関連する会社が存在することを確認
        $this->assertNotNull($site->company);
        $this->assertInstanceOf(Company::class, $site->company);
        $this->assertDatabaseHas('companies', [
            'id' => $site->company_id,
        ]);
    }
}
