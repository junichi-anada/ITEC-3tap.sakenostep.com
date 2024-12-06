<?php

namespace App\Services\Site\Actions;

use App\Models\Site;
use App\Services\Site\DTOs\SiteData;
use App\Services\Site\Exceptions\SiteException;
use App\Services\Site\Traits\SiteTransactionTrait;
use Illuminate\Support\Facades\Log;

class CreateSiteAction
{
    use SiteTransactionTrait;

    /**
     * 新しいサイトを作成する
     *
     * @param SiteData $data
     * @return Site
     * @throws SiteException
     */
    public function execute(SiteData $data): Site
    {
        return $this->executeTransaction(
            function () use ($data) {
                Log::info("Creating new site with data: " . json_encode($data->toArray()));
                
                $site = Site::create($data->toArray());
                
                if (!$site) {
                    throw SiteException::createFailed($data->toArray());
                }
                
                return $site;
            },
            'サイトの作成に失敗しました',
            $data->toArray()
        );
    }
}
