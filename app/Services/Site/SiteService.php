<?php

declare(strict_types=1);

namespace App\Services\Site;

use App\Models\Site;
use App\Services\Site\Actions\CreateSiteAction;
use App\Services\Site\Actions\UpdateSiteAction;
use App\Services\Site\Actions\DeleteSiteAction;
use App\Services\Site\DTOs\SiteData;
use App\Services\ServiceErrorHandler;
use Illuminate\Database\Eloquent\Collection;

/**
 * サイトサービスクラス
 *
 * このクラスはサイトに関する操作のファサードとして機能し、
 * 具体的な処理を各Actionクラスに委譲します。
 */
final class SiteService
{
    use ServiceErrorHandler;

    public function __construct(
        private CreateSiteAction $createSiteAction,
        private UpdateSiteAction $updateSiteAction,
        private DeleteSiteAction $deleteSiteAction
    ) {}

    /**
     * 新しいサイトを作成する
     *
     * @param array $data
     * @return Site
     */
    public function create(array $data): Site
    {
        return $this->createSiteAction->execute(SiteData::fromArray($data));
    }

    /**
     * サイト情報を更新する
     *
     * @param Site $site
     * @param array $data
     * @return Site
     */
    public function update(Site $site, array $data): Site
    {
        return $this->updateSiteAction->execute($site, SiteData::fromArray($data));
    }

    /**
     * サイトを削除する
     *
     * @param Site $site
     * @return bool
     */
    public function delete(Site $site): bool
    {
        return $this->deleteSiteAction->execute($site);
    }

    /**
     * 全てのサイト情報を取得する
     *
     * @return Collection|null
     */
    public function getAllSites(): ?Collection
    {
        return $this->tryCatchWrapper(
            fn () => Site::all(),
            '全てのサイト情報の取得に失敗しました'
        );
    }

    /**
     * サイトコードでサイトを取得する
     *
     * @param string $siteCode
     * @return Site|null
     */
    public function getSiteByCode(string $siteCode): ?Site
    {
        return $this->tryCatchWrapper(
            fn () => Site::where('site_code', $siteCode)->first(),
            'site_codeによるサイト情報の取得に失敗しました',
            ['site_code' => $siteCode]
        );
    }

    /**
     * IDでサイトを取得する
     *
     * @param int $siteId
     * @return Site|null
     */
    public function getSiteById(int $siteId): ?Site
    {
        return $this->tryCatchWrapper(
            fn () => Site::find($siteId),
            'site_idによるサイト情報の取得に失敗しました',
            ['site_id' => $siteId]
        );
    }
}
