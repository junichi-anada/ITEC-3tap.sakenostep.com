<?php

namespace App\Services\Auth\Actions;

use App\Models\Authenticate;
use App\Services\Auth\DTOs\UserAuthenticationData;
use App\Services\Auth\Exceptions\AuthenticationException;
use App\Services\ServiceErrorHandler;
use Illuminate\Support\Facades\Hash;

class AuthenticateUserAction
{
    use ServiceErrorHandler;

    public function execute(UserAuthenticationData $data): Authenticate
    {
        return $this->tryCatchWrapper(function () use ($data) {
            $auth = Authenticate::where('login_code', $data->loginCode)
                              ->where('site_id', $data->siteId)
                              ->first();

            if (!$auth || !Hash::check($data->password, $auth->password)) {
                throw AuthenticationException::invalidCredentials();
            }

            return $auth;
        }, '認証に失敗しました', [
            'login_code' => $data->loginCode,
            'site_id' => $data->siteId
        ]);
    }
}
