<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CompanyFactoryのテストクラス
 */
final class CompanyFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * CompanyFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_company()
    {
        $company = Company::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'company_code' => $company->company_code,
            'company_name' => $company->company_name,
            'name' => $company->name,
            'postal_code' => $company->postal_code,
            'address' => $company->address,
            'phone' => $company->phone,
            'phone2' => $company->phone2,
            'fax' => $company->fax,
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $company->company_code);
        $this->assertNotEmpty($company->company_name);
        $this->assertNotEmpty($company->name);
        $this->assertNotEmpty($company->postal_code);
        $this->assertNotEmpty($company->address);
        $this->assertNotEmpty($company->phone);
        $this->assertTrue(is_null($company->phone2) || is_string($company->phone2));
        $this->assertTrue(is_null($company->fax) || is_string($company->fax));
    }

    /**
     * CompanyFactoryが一意なcompany_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_company_code()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $this->assertNotEquals($company1->company_code, $company2->company_code);
    }

    /**
     * CompanyFactoryのwithPublishedSites状態が正しく関連サイトを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_company_with_published_sites()
    {
        $siteCount = 3;
        $company = Company::factory()->withPublishedSites($siteCount)->create();

        // 関連するサイトが指定された数だけ作成されていることを確認
        $this->assertCount($siteCount, $company->sites);

        foreach ($company->sites as $site) {
            $this->assertDatabaseHas('sites', [
                'id' => $site->id,
                'company_id' => $company->id,
            ]);
            $this->assertNotNull($site->site_code);
            $this->assertNotEmpty($site->name);
        }
    }
}
