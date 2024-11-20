<?php

namespace App\Services\Site;

use App\Models\Site;
use Illuminate\Support\Facades\Log;

class SiteReadService
{
    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private static function tryCatchWrapper($callback, $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error($errorMessage . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Siteテーブルから全てのサイト情報を取得します。
     *
     * @return \Illuminate\Database\Eloquent\Collection | null
     */
    public static function getAllSites()
    {
        return self::tryCatchWrapper(function () {
            return Site::all();
        }, '全てのサイト情報の取得に失敗しました');
    }

    /**
     * 指定されたsite_codeに一致するサイト情報を取得します。
     *
     * @param string $siteCode
     * @return Site|null
     */
    public static function getSiteByCode($siteCode)
    {
        return self::tryCatchWrapper(function () use ($siteCode) {
            return Site::where('site_code', $siteCode)->first();
        }, 'site_codeによるサイト情報の取得に失敗しました');
    }

    /**
     * 指定されたsite_idに一致するサイト情報を取得します。
     *
     * @param int $siteId
     * @return Site|null
     */
    public static function getSiteById($siteId)
    {
        return self::tryCatchWrapper(function () use ($siteId) {
            return Site::find($siteId);
        }, 'site_idによるサイト情報の取得に失敗しました');
    }
}
