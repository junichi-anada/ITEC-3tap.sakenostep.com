<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * UserFactoryのテストクラス
 */
final class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UserFactoryが正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user()
    {
        $user = User::factory()->create();

        // データベースにレコードが存在することを確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'user_code' => $user->user_code,
            'site_id' => $user->site_id,
            'name' => $user->name,
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'phone' => $user->phone,
            'phone2' => $user->phone2,
            'fax' => $user->fax,
            // パスワードはハッシュ化されているため直接確認はしない
        ]);

        // 属性が期待通りであることを確認
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $user->user_code);
        $this->assertInstanceOf(Site::class, $user->site);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->postal_code);
        $this->assertNotEmpty($user->address);
        $this->assertNotEmpty($user->phone);
        $this->assertTrue(is_null($user->phone2) || is_string($user->phone2));
        $this->assertTrue(is_null($user->fax) || is_string($user->fax));
    }

    /**
     * UserFactoryが一意なuser_codeを生成することをテストします。
     *
     * @return void
     */
    public function test_it_generates_unique_user_code()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertNotEquals($user1->user_code, $user2->user_code);
    }

    /**
     * UserFactoryが特定のサイトに関連付けられたユーザーを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_for_specific_site()
    {
        $site = Site::factory()->create();
        $user = User::factory()->forSite($site)->create();

        $this->assertEquals($site->id, $user->site_id);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'site_id' => $site->id,
        ]);
    }

    /**
     * UserFactoryがadmin状態のユーザーを正しく生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_admin_user()
    {
        $adminUser = User::factory()->admin()->create();

        $this->assertStringStartsWith('ADMIN', $adminUser->user_code);
        $this->assertEquals('管理者', $adminUser->name);
        $this->assertDatabaseHas('users', [
            'id' => $adminUser->id,
            'name' => '管理者',
        ]);
    }

    /**
     * UserFactoryが特定の名前を持つユーザーを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_with_specific_name()
    {
        $specificName = '田中 一郎';
        $user = User::factory()->withName($specificName)->create();

        $this->assertEquals($specificName, $user->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $specificName,
        ]);
    }

    /**
     * UserFactoryが複数の状態を組み合わせて正しくモデルを生成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_user_with_multiple_states()
    {
        $site = Site::factory()->create();
        $specificName = '佐藤 二郎';

        $user = User::factory()
            ->forSite($site)
            ->withName($specificName)
            ->create();

        $this->assertEquals($site->id, $user->site_id);
        $this->assertEquals($specificName, $user->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'site_id' => $site->id,
            'name' => $specificName,
        ]);
    }

    /**
     * UserFactoryが関連するサイトを正しく作成することをテストします。
     *
     * @return void
     */
    public function test_it_creates_related_site()
    {
        $user = User::factory()->create();

        // 関連するサイトが存在することを確認
        $this->assertNotNull($user->site);
        $this->assertInstanceOf(Site::class, $user->site);
        $this->assertDatabaseHas('sites', [
            'id' => $user->site_id,
        ]);
    }
}
