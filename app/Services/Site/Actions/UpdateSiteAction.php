<?php

namespace App\Services\Site\Actions;

use App\Models\Site;
use App\Services\Site\DTOs\SiteData;
use App\Services\Site\Exceptions\SiteException;
use App\Services\Site\Traits\SiteTransactionTrait;
use Illuminate\Support\Facades\Log;

class UpdateSiteAction
{
    use SiteTransactionTrait;

    /**
     * サイト情報を更新する
     *
     * @param Site $site
     * @param SiteData $data
     * @return Site
     * @throws SiteException
     */
    public function execute(Site $site, SiteData $data): Site
    {
        return $this->executeTransaction(
            function () use ($site, $data) {
                Log::info("Updating site ID: {$site->id}");

                $updated = $site->update($data->toArray());
                if (!$updated) {
                    throw SiteException::updateFailed($site->id);
                }

                return $site->fresh();
            },
            'サイトの更新に失敗しました',
            ['id' => $site->id] + $data->toArray()
        );
    }
}
