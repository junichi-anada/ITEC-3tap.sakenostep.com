<?php

namespace App\Services\Site;

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiteUpdateService
{
    /**
     * サイト情報を更新する
     *
     * @param Site $site
     * @param array $data
     * @return Site|null
     */
    public function update(Site $site, array $data)
    {
        return $this->executeTransaction(function () use ($site, $data) {
            $site->update($data);
            return $site;
        }, 'サイトの更新に失敗しました');
    }

    private function executeTransaction($callback, $errorMessage)
    {
        try {
            return DB::transaction($callback);
        } catch (\Exception $e) {
            Log::error($errorMessage . ': ' . $e->getMessage());
            return null;
        }
    }
}
