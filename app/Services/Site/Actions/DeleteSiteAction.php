<?php

namespace App\Services\Site\Actions;

use App\Models\Site;
use App\Services\Site\Exceptions\SiteException;
use App\Services\Site\Traits\SiteTransactionTrait;
use Illuminate\Support\Facades\Log;

class DeleteSiteAction
{
    use SiteTransactionTrait;

    /**
     * サイトを削除する
     *
     * @param Site $site
     * @return bool
     * @throws SiteException
     */
    public function execute(Site $site): bool
    {
        return $this->executeTransaction(
            function () use ($site) {
                Log::info("Deleting site ID: {$site->id}");

                $deleted = $site->delete();
                if (!$deleted) {
                    throw SiteException::deleteFailed($site->id);
                }

                return $deleted;
            },
            'サイトの削除に失敗しました',
            ['id' => $site->id]
        );
    }
}
