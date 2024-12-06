<?php

namespace App\Services\Item\Actions\OperatorItemActions;

use App\Models\Item;
use App\Services\Item\Exceptions\ItemException;
use App\Services\Traits\OperatorActionTrait;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteItemAction
{
    use OperatorActionTrait;

    /**
     * 商品を削除します
     *
     * @param int $itemId
     * @param int $operatorId
     * @return bool
     * @throws ItemException
     */
    public function execute(int $itemId, int $operatorId): bool
    {
        if (!$this->hasPermission($operatorId)) {
            throw ItemException::deleteFailed($itemId, 'Operator does not have permission');
        }

        $item = Item::find($itemId);
        if (!$item) {
            throw ItemException::notFound($itemId);
        }

        try {
            DB::beginTransaction();

            // 関連データの確認
            if ($this->hasActiveOrders($item)) {
                throw ItemException::deleteFailed($itemId, 'Item has active orders');
            }

            // 論理削除を実行
            $item->is_active = false;
            $item->save();

            $this->logOperation($operatorId, 'item.delete', [
                'item_id' => $item->id,
                'item_name' => $item->name
            ]);

            DB::commit();

            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw ItemException::deleteFailed($itemId, $e->getMessage());
        }
    }

    /**
     * アクティブな注文が存在するか確認します
     *
     * @param Item $item
     * @return bool
     */
    private function hasActiveOrders(Item $item): bool
    {
        return $item->orderDetails()
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled');
            })
            ->exists();
    }
}
