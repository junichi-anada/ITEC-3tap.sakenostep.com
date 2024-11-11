<?php

namespace App\Services\Site\Customer;

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteService
{
    /**
     * サイトを削除する
     *
     * @param Site $site
     * @return bool|null
     */
    public function delete(Site $site)
    {
        return $this->executeTransaction(function () use ($site) {
            return $site->delete();
        }, 'サイトの削除に失敗しました');
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
