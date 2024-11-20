<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\SiteAuthProvider;
use App\Models\Site;
use App\Models\AuthProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * SiteAuthProviderFactoryのテストクラス
 */
final class SiteAuthProviderFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SiteAuthProviderFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_auth_provider()
    {
        $siteAuthProvider = SiteAuthProvider::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('site_auth_providers', [
            'id' => $siteAuthProvider->id,
            'site_id' => $siteAuthProvider->site_id,
            'auth_provider_id' => $siteAuthProvider->auth_provider_id,
            'is_enabled' => $siteAuthProvider->is_enabled,
        ]);

        // 属性が期待通りであることを確認
        $this->assertInstanceOf(Site::class, $siteAuthProvider->site);
        $this->assertInstanceOf(AuthProvider::class, $siteAuthProvider->authProvider);
        $this->assertIsBool($siteAuthProvider->is_enabled);
    }

    /**
     * SiteAuthProviderFactoryが特定の認証プロバイダーで受信者を生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_auth_provider_for_specific_provider()
    {
        $provider = AuthProvider::factory()->create(['name' => 'GitHub']);
        $siteAuthProvider = SiteAuthProvider::factory()->forAuthProvider($provider)->create();

        $this->assertEquals($provider->id, $siteAuthProvider->auth_provider_id);
        $this->assertDatabaseHas('site_auth_providers', [
            'id' => $siteAuthProvider->id,
            'auth_provider_id' => $provider->id,
        ]);
    }

    /**
     * SiteAuthProviderFactoryが特定のサイトで受信者を生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_site_auth_provider_for_specific_site()
    {
        $site = Site::factory()->create(['name' => 'メインサイト']);
        $siteAuthProvider = SiteAuthProvider::factory()->forSite($site)->create();

        $this->assertEquals($site->id, $siteAuthProvider->site_id);
        $this->assertDatabaseHas('site_auth_providers', [
            'id' => $siteAuthProvider->id,
            'site_id' => $site->id,
        ]);
    }

    /**
     * SiteAuthProviderFactoryがenabled状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_enabled_site_auth_provider()
    {
        $siteAuthProvider = SiteAuthProvider::factory()->enabled()->create();

        $this->assertTrue($siteAuthProvider->is_enabled);
        $this->assertDatabaseHas('site_auth_providers', [
            'id' => $siteAuthProvider->id,
            'is_enabled' => true,
        ]);
    }

    /**
     * SiteAuthProviderFactoryがdisabled状態を正しく設定することをテストします。
     *
     * @return void
     */
    public function test_it_creates_disabled_site_auth_provider()
    {
        $siteAuthProvider = SiteAuthProvider::factory()->disabled()->create();

        $this->assertFalse($siteAuthProvider->is_enabled);
        $this->assertDatabaseHas('site_auth_providers', [
            'id' => $siteAuthProvider->id,
            'is_enabled' => false,
        ]);
    }

    /**
     * SiteAuthProviderFactoryが関連するサイトと認証プロバイダーを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site_and_auth_provider()
    {
        $site = Site::factory()->create();
        $provider = AuthProvider::factory()->create();
        $siteAuthProvider = SiteAuthProvider::factory()->create([
            'site_id' => $site->id,
            'auth_provider_id' => $provider->id,
        ]);

        $this->assertEquals($site->id, $siteAuthProvider->site_id);
        $this->assertEquals($provider->id, $siteAuthProvider->auth_provider_id);
        $this->assertInstanceOf(Site::class, $siteAuthProvider->site);
        $this->assertInstanceOf(AuthProvider::class, $siteAuthProvider->authProvider);
    }
}
