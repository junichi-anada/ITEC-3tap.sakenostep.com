<?php
declare(strict_types=1);

/**
 * LoginControllerTestクラス
 *
 * このテストクラスは、LoginControllerの機能を検証します。
 * 主な仕様:
 * - ログインページの表示
 * - ユーザーのログイン処理
 * - ログアウト処理
 * 制限事項:
 * - モックを使用してAuthServiceの依存関係を置き換えています。
 */

namespace Tests\Feature\Web\Auth;

use Tests\TestCase;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Mockery;
use App\Models\Site;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var AuthService モックされたAuthServiceインスタンス
     */
    private AuthService $authServiceMock;

    /**
     * セットアップ処理
     * AuthServiceのモックを作成し、LoginControllerに注入します。
     * 必要なデータベースの初期データを作成します。
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 必要なSiteを作成
        Site::factory()->create();

        // CSRFミドルウェアを無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->authServiceMock = Mockery::mock(AuthService::class);
        $this->app->instance(AuthService::class, $this->authServiceMock);
    }

    /**
     * ログインページが正しく表示されることをテストします。
     *
     * @return void
     */
    public function testIndexDisplaysLoginPage(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('index');
    }

    /**
     * 正しい認証情報でユーザーがログインできることをテストします。
     *
     * @return void
     */
    public function testLoginWithValidCredentials(): void
    {
        $credentials = [
            'login_code' => 'validLogin',
            'password' => 'validPassword',
            'site_code' => 'validSite',
        ];

        $site = Site::where('code', $credentials['site_code'])->first();
        if (!$site) {
            $site = Site::factory()->create(['code' => $credentials['site_code']]);
        }

        $user = (object) [
            'id' => 1,
            'entity_type' => 'App\Models\User',
            'touch' => function () {}
        ];

        $this->authServiceMock
            ->shouldReceive('validateSite')
            ->with($credentials['site_code'])
            ->once()
            ->andReturn($site);

        $this->authServiceMock
            ->shouldReceive('authenticateUser')
            ->with($credentials['login_code'], $site->id, $credentials['password'])
            ->once()
            ->andReturn($user);

        $response = $this->post(route('login'), $credentials);

        $response->assertRedirect(route('user.order.item.list'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 無効な認証情報でログインが失敗することをテストします。
     *
     * @return void
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $credentials = [
            'login_code' => 'invalidLogin',
            'password' => 'invalidPassword',
            'site_code' => 'invalidSite',
        ];

        $this->authServiceMock
            ->shouldReceive('validateSite')
            ->with($credentials['site_code'])
            ->once()
            ->andThrow(new \Illuminate\Validation\ValidationException(
                validator: \Illuminate\Support\Facades\Validator::make([], []),
                errorBag: 'default',
                status: 422
            ));

        $response = $this->from(route('login'))->post(route('login'), $credentials);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['login_code']);
        $this->assertGuest();
    }

    /**
     * ユーザーがログアウトできることをテストします。
     *
     * @return void
     */
    public function testLogout(): void
    {
        // ユーザーを認証状態にする
        $user = \App\Models\User::factory()->create();
        $this->be($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * テスト後のクリーンアップ処理
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
