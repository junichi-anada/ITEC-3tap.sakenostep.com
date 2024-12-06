<?php

namespace App\Services\Item\Actions;

use App\Models\Item;
use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GetRecommendedItemsAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository
    ) {}

    /**
     * おすすめ商品を取得する
     *
     * @param int $siteId
     * @param int $limit
     * @return Collection
     * @throws ItemException
     */
    public function execute(int $siteId, int $limit = 10): Collection
    {
        return $this->tryCatchWrapper(
            function () use ($siteId, $limit) {
                Log::info("Getting recommended items for site: $siteId, limit: $limit");
                
                $items = $this->repository->findRecommendedItems($siteId, $limit);
                
                if ($items->isEmpty()) {
                    Log::warning("No recommended items found for site: $siteId");
                }

                return $items;
            },
            'おすすめ商品の取得に失敗しました',
            ['site_id' => $siteId, 'limit' => $limit]
        );
    }
}
