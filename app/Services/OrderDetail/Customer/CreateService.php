<?php
/**
 * 注文詳細の作成サービス
 */
namespace App\Services\OrderDetail\Customer;

use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Services\Order\Customer\CreateService as OrderCreateService;
use Illuminate\Support\Str;

class CreateService
{
    /**
     * 注文基本データ作成サービス
     */
    private $orderCreateService;


    /**
     * コンストラクタ
     */
    public function __construct(OrderCreateService $orderCreateService)
    {
        $this->orderCreateService = $orderCreateService;
    }

    /**
     * 例外処理を共通化するためのラッパーメソッドです。
     *
     * @param \Closure $callback
     * @param string $errorMessage
     * @return mixed
     */
    private function tryCatchWrapper(\Closure $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error("Error: $errorMessage - " . $e->getMessage());
            return null;
        }
    }

    /**
     * しい注文詳細を作する
     *
     * @param array $data
     * @return OrderDetail|null
     */
    public function create(array $data)
    {
        Log::info("Creating new order detail with data: " . json_encode($data));

        // detail_code を生成
        do {
            $data['detail_code'] = Str::ulid();
        } while (OrderDetail::where('detail_code', $data['detail_code'])->exists());

        // unit_price を設定
        $data['unit_price'] = $data['item']->unit_price;

        // unit_name を設定
        $data['unit_name'] = $data['item']->unit->name ?? 'default_unit'; // Item モデルの unit リレーションを使用

        // price を設定
        $data['price'] = $data['unit_price'] * $data['volume'];

        // tax を設定
        $data['tax'] = $data['price'] * 0.1;

        return $this->tryCatchWrapper(function () use ($data) {
            return OrderDetail::create($data);
        }, '注文詳細の作成に失敗しました');
    }

    /**
     * 注文基本データと注文詳細データを一挙に作成する
     *
     * @param int $siteId
     * @param int $userId
     * @param Item $item
     * @param int $volume
     * @return OrderDetail|null
     */
    public function createOrderWithDetails($siteId, $userId, $item, $volume)
    {
        Log::info("Creating order with details for site: $siteId, user: $userId, item: {$item->id}, volume: $volume");

        $order = $this->orderCreateService->createBaseOrder($siteId, $userId);
        if (!$order) {
            Log::error("Failed to create base order for site: $siteId, user: $userId");
            return null;
        }

        $orderDetail = $this->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'volume' => $volume,
            'item' => $item,
        ]);

        if (!$orderDetail) {
            Log::error("Failed to create order detail for order: {$order->id}, item: {$item->id}");
        }

        return $orderDetail;
    }
}
