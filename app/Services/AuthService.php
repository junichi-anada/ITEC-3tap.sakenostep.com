<?php

namespace App\Services;

use App\Models\Authenticate;
use App\Models\Site;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function validateSite($siteCode)
    {
        $site = Site::where('site_code', $siteCode)->first();
        if (!$site) {
            throw ValidationException::withMessages([
                'site_code' => __('無効なサイトコードです。'),
            ]);
        }
        return $site;
    }

    public function authenticateUser($loginCode, $siteId, $password)
    {
        $auth = Authenticate::where('login_code', $loginCode)
                            ->where('site_id', $siteId)
                            ->first();

        if ($auth && Hash::check($password, $auth->password)) {
            return $auth;
        }

        throw ValidationException::withMessages([
            'login_code' => __('電話番号またはお客様番号に誤りがあります。'),
        ]);
    }
}
