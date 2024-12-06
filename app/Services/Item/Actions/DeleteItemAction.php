<?php

namespace App\Services\Item\Actions;

use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
use Illuminate\Support\Facades\Log;

class DeleteItemAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository
    ) {}

    /**
     * 商品を削除する
     *
     * @param string $itemCode
     * @param int $siteId
     * @return bool
     * @throws ItemException
     */
    public function execute(string $itemCode, int $siteId): bool
    {
        return $this->tryCatchWrapper(
            function () use ($itemCode, $siteId) {
                Log::info("Deleting item with code: $itemCode, site ID: $siteId");

                $item = $this->repository->findByCode($itemCode, $siteId);
                if (!$item) {
                    throw ItemException::notFound($itemCode);
                }

                $result = $this->repository->delete($item->id);
                if (!$result) {
                    throw ItemException::deleteFailed($itemCode);
                }

                return $result;
            },
            '商品の削除に失敗しました',
            ['item_code' => $itemCode, 'site_id' => $siteId]
        );
    }
}
