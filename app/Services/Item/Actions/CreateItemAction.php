<?php

namespace App\Services\Item\Actions;

use App\Models\Item;
use App\Services\Item\DTOs\ItemData;
use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
use Illuminate\Support\Facades\Log;

class CreateItemAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository
    ) {}

    /**
     * 新しい商品を作成する
     *
     * @param ItemData $data
     * @return Item
     * @throws ItemException
     */
    public function execute(ItemData $data): Item
    {
        return $this->tryCatchWrapper(
            function () use ($data) {
                Log::info("Creating new item with data: " . json_encode($data->toArray()));
                
                $item = $this->repository->create($data->toArray());
                
                if (!$item) {
                    throw ItemException::createFailed($data->toArray());
                }
                
                return $item;
            },
            '商品の作成に失敗しました',
            $data->toArray()
        );
    }
}
