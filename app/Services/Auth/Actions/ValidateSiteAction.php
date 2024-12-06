<?php

namespace App\Services\Auth\Actions;

use App\Models\Site;
use App\Services\Auth\DTOs\SiteValidationData;
use App\Services\Auth\Exceptions\AuthenticationException;
use App\Services\ServiceErrorHandler;

class ValidateSiteAction
{
    use ServiceErrorHandler;

    public function execute(SiteValidationData $data): Site
    {
        return $this->tryCatchWrapper(function () use ($data) {
            $site = Site::where('site_code', $data->siteCode)->first();
            
            if (!$site) {
                throw AuthenticationException::invalidSite($data->siteCode);
            }

            return $site;
        }, 'サイト検証に失敗しました', ['site_code' => $data->siteCode]);
    }
}
