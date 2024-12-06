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
     * @return bool
     * @throws ItemException
     */
    public function execute(string $itemCode): bool
    {
        return $this->tryCatchWrapper(
            function () use ($itemCode) {
                Log::info("Deleting item with code: $itemCode");
                
                $item = $this->repository->findByCode($itemCode);
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
            ['item_code' => $itemCode]
        );
    }
}
