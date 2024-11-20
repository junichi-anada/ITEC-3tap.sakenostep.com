<?php

declare(strict_types=1);

/**
 * AuthServiceクラスのテスト
 *
 * 認証サービスの機能を検証します。
 *
 * @category テスト
 * @package Tests\Unit\Services\Auth
 * @version 1.0
 */

namespace Tests\Unit\Services\Auth;

use App\Models\Authenticate;
use App\Services\Auth\AuthService;
use App\Services\Site\SiteReadService as SiteReadService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    /**
     * サイト読み取りサービスのモック
     *
     * @var \App\Services\Site\Customer\ReadService|\Mockery\MockInterface
     */
    protected $siteReadServiceMock;

    /**
     * 認証サービスインスタンス
     *
     * @var \App\Services\Auth\AuthService
     */
    protected AuthService $authService;

    /**
     * テストセットアップ
     *
     * サービスのインスタンスとモックを初期化します。
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // サイト読み取りサービスのモックを作成
        $this->siteReadServiceMock = Mockery::mock(SiteReadService::class);

        // AuthServiceのインスタンスを生成し、モックを注入
        $this->authService = new AuthService($this->siteReadServiceMock);
    }

    /**
     * サイトコードが有効な場合、サイト情報を返すことをテスト
     *
     * @return void
     */
    public function testValidateSiteReturnsSiteWhenSiteCodeIsValid(): void
    {
        // Arrange
        $siteCode = 'valid_site_code';
        $site = (object)['id' => 1, 'code' => $siteCode, 'name' => 'Valid Site'];

        // モックが特定のサイトコードでサイトを返すよう設定
        $this->siteReadServiceMock
            ->shouldReceive('getSiteByCode')
            ->with($siteCode)
            ->once()
            ->andReturn($site);

        // Act
        $result = $this->authService->validateSite($siteCode);

        // Assert
        $this->assertEquals($site, $result);
    }

    /**
     * サイトコードが無効な場合、ValidationExceptionを投げることをテスト
     *
     * @return void
     */
    public function testValidateSiteThrowsExceptionWhenSiteCodeIsInvalid(): void
    {
        // Arrange
        $siteCode = 'invalid_site_code';

        // モックが特定のサイトコードでnullを返すよう設定
        $this->siteReadServiceMock
            ->shouldReceive('getSiteByCode')
            ->with($siteCode)
            ->once()
            ->andReturn(null);

        // Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('無効なサイトコードです。');

        // Act
        $this->authService->validateSite($siteCode);
    }

    /**
     * 認証情報が有効な場合、Authenticateモデルを返すことをテスト
     *
     * @return void
     */
    public function testAuthenticateUserReturnsAuthWhenCredentialsAreValid(): void
    {
        // Arrange
        $loginCode = 'valid_login_code';
        $siteId = 1;
        $password = 'securepassword';
        $hashedPassword = Hash::make($password);

        // Authenticateモデルのインスタンスを作成
        $auth = new Authenticate([
            'login_code' => $loginCode,
            'site_id' => $siteId,
            'password' => $hashedPassword,
        ]);

        // Authenticateモデルのクエリビルダーをモック
        Authenticate::shouldReceive('where')
            ->with('login_code', $loginCode)
            ->andReturnSelf();

        Authenticate::shouldReceive('where')
            ->with('site_id', $siteId)
            ->andReturnSelf();

        Authenticate::shouldReceive('first')
            ->once()
            ->andReturn($auth);

        // Act
        $result = $this->authService->authenticateUser($loginCode, $siteId, $password);

        // Assert
        $this->assertEquals($auth, $result);
    }

    /**
     * 認証情報が無効な場合、ValidationExceptionを投げることをテスト
     *
     * @return void
     */
    public function testAuthenticateUserThrowsExceptionWhenCredentialsAreInvalid(): void
    {
        // Arrange
        $loginCode = 'invalid_login_code';
        $siteId = 1;
        $password = 'wrongpassword';

        // Authenticateモデルのクエリビルダーをモック
        Authenticate::shouldReceive('where')
            ->with('login_code', $loginCode)
            ->andReturnSelf();

        Authenticate::shouldReceive('where')
            ->with('site_id', $siteId)
            ->andReturnSelf();

        Authenticate::shouldReceive('first')
            ->once()
            ->andReturn(null);

        // Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('電話番号またはお客様番号に誤りがあります。');

        // Act
        $this->authService->authenticateUser($loginCode, $siteId, $password);
    }

    /**
     * テスト後のクリーンアップ
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
