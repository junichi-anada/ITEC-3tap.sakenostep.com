<?php

namespace App\Services\Item\Actions\OperatorItemActions;

use App\Models\Item;
use App\Services\Item\DTOs\ItemData;
use App\Services\Item\Exceptions\ItemException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CreateItemAction
{
    use OperatorActionTrait;

    /**
     * 商品を作成します
     *
     * @param ItemData $data
     * @param int $operatorId
     * @return ItemData
     * @throws ItemException
     */
    public function execute(ItemData $data, int $operatorId): ItemData
    {
        if (!$this->hasPermission($operatorId)) {
            throw ItemException::createFailed('Operator does not have permission');
        }

        $this->validateData($data);

        try {
            DB::beginTransaction();

            $item = new Item();
            $item->name = $data->name;
            $item->description = $data->description;
            $item->price = $data->price;
            $item->category_id = $data->categoryId;
            $item->is_active = $data->isActive;
            $item->metadata = $data->metadata;
            $item->save();

            $this->logOperation($operatorId, 'item.create', [
                'item_id' => $item->id,
                'item_name' => $item->name
            ]);

            DB::commit();

            return ItemData::fromArray($item->toArray());
        } catch (Throwable $e) {
            DB::rollBack();
            throw ItemException::createFailed($e->getMessage());
        }
    }

    /**
     * データのバリデーションを行います
     *
     * @param ItemData $data
     * @throws ItemException
     */
    private function validateData(ItemData $data): void
    {
        $validator = Validator::make($data->toArray(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:item_categories,id',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw ItemException::invalidData($validator->errors()->first());
        }
    }
}
