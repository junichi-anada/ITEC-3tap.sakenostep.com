<?php

namespace App\Services\Auth;

use App\Models\Authenticate;
use App\Models\Site;
use App\Services\Auth\Actions\ValidateSiteAction;
use App\Services\Auth\Actions\AuthenticateUserAction;
use App\Services\Auth\DTOs\SiteValidationData;
use App\Services\Auth\DTOs\UserAuthenticationData;
use App\Services\ServiceErrorHandler;

class AuthService
{
    use ServiceErrorHandler;

    public function __construct(
        private ValidateSiteAction $validateSiteAction,
        private AuthenticateUserAction $authenticateUserAction
    ) {}

    /**
     * サイトコードの検証
     *
     * @param string $siteCode
     * @return Site
     * @throws \App\Services\Auth\Exceptions\AuthenticationException
     */
    public function validateSite(string $siteCode): Site
    {
        $data = new SiteValidationData($siteCode);
        return $this->validateSiteAction->execute($data);
    }

    /**
     * ユーザー認証
     *
     * @param string $loginCode
     * @param int $siteId
     * @param string $password
     * @return Authenticate
     * @throws \App\Services\Auth\Exceptions\AuthenticationException
     */
    public function authenticateUser(string $loginCode, int $siteId, string $password): Authenticate
    {
        $data = new UserAuthenticationData($loginCode, $siteId, $password);
        return $this->authenticateUserAction->execute($data);
    }
}
