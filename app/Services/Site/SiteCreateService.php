<?php

namespace App\Services\Site;

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiteCreateService
{
    /**
     * 新しいサイトを作成する
     *
     * @param array $data
     * @return Site|null
     */
    public function create(array $data)
    {
        return $this->executeTransaction(function () use ($data) {
            return Site::create($data);
        }, 'サイトの作成に失敗しました');
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
