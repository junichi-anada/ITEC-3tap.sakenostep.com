<?php
/**
 * 注文の作成サービス
 */
namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OrderDetail\OrderDetailCreateService as OrderDetailCreateService;

class OrderCreateService
{
    protected $orderDetailCreateService;

    public function __construct(OrderDetailCreateService $orderDetailCreateService)
    {
        $this->orderDetailCreateService = $orderDetailCreateService;
    }

    /**
     * 新しい注文を作成する
     *
     * @param array $data
     * @return Order|null
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = $this->createBaseOrder($data['site_id'], $data['user_id']);

            $orderDetail = $this->orderDetailCreateService->create($order->id, $data['item'], $data['volume']);

            return $orderDetail;
        });
    }

    public function createBaseOrder($siteId, $userId)
    {
        do {
            $orderCode = Str::ulid();
        } while (Order::where('order_code', $orderCode)->exists());

        return Order::create([
            'order_code' => $orderCode,
            'site_id' => $siteId,
            'user_id' => $userId,
        ]);
    }
}

