<?php

namespace App\Services\Item\Actions;

use App\Models\Item;
use App\Services\Item\DTOs\ItemData;
use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
use Illuminate\Support\Facades\Log;

class UpdateItemAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository
    ) {}

    /**
     * 商品を更新する
     *
     * @param string $itemCode
     * @param ItemData $data
     * @return bool
     * @throws ItemException
     */
    public function execute(string $itemCode, ItemData $data): bool
    {
        return $this->tryCatchWrapper(
            function () use ($itemCode, $data) {
                Log::info("Updating item with code: $itemCode");
                
                $item = $this->repository->findByCode($itemCode);
                if (!$item) {
                    throw ItemException::notFound($itemCode);
                }

                $result = $this->repository->update($item->id, $data->toArray());
                if (!$result) {
                    throw ItemException::updateFailed($itemCode);
                }

                return $result;
            },
            '商品の更新に失敗しました',
            ['item_code' => $itemCode] + $data->toArray()
        );
    }
}
