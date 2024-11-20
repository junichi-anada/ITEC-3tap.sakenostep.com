<?php
/**
 * 認証サービス
 *
 * @category サービス
 * @package App\Services\Auth
 * @version 1.0
 */

namespace App\Services\Auth;

use App\Models\Authenticate;
use App\Services\Site\SiteReadService as SiteReadService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Transaction\TransactionService;

class AuthService
{
    protected $siteReadService;
    protected TransactionService $transactionService;

    public function __construct(SiteReadService $siteReadService, TransactionService $transactionService)
    {
        $this->siteReadService = $siteReadService;
        $this->transactionService = $transactionService;
    }

    public function validateSite($siteCode)
    {
        $site = $this->siteReadService->getSiteByCode($siteCode);
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
