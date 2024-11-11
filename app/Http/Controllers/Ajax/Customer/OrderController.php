<?php
/**
 * 顧客向け注文管理機能 Api用コントローラー
 *
 * @author J.AnadA <anada@re-buysell.jp>
 * @version 1.0.0
 * @copyright 2024 ItecSystem co ltd.
 */
namespace App\Http\Controllers\Ajax\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Services\Order\Customer\ReadService as OrderReadService;
use App\Services\Order\Customer\CreateService as OrderCreateService;
use App\Services\Order\Customer\UpdateService as OrderUpdateService;
use App\Services\Item\Customer\ReadService as ItemReadService;
use App\Services\OrderDetail\Customer\ReadService as OrderDetailReadService;
use App\Services\OrderDetail\Customer\CreateService as OrderDetailCreateService;
use App\Services\OrderDetail\Customer\DeleteService as OrderDetailDeleteService;

class OrderController extends Controller
{
    const SUCCESS_MESSAGE = '注文リストに追加しました';
    const DELETE_SUCCESS_MESSAGE = '注文リストから削除しました';
    const DELETE_ALL_SUCCESS_MESSAGE = '注文リストから全て削除しました';
    const ORDER_SUCCESS_MESSAGE = '注文しました';
    const VALIDATION_ERROR_MESSAGE = 'バリデーションエラー';
    const UNEXPECTED_ERROR_MESSAGE = '予期しないエラーが発生しました';
    const INVALID_DETAIL_CODE_MESSAGE = '無効な注文詳細コードです';
    const NOT_FOUND_MESSAGE = '注文詳細データが見つかりません';

    protected $orderReadService;
    protected $orderCreateService;
    protected $orderUpdateService;
    protected $itemReadService;
    protected $orderDetailReadService;
    protected $orderDetailCreateService;
    protected $orderDetailDeleteService;

    public function __construct(
        OrderReadService $orderReadService,
        OrderCreateService $orderCreateService,
        OrderUpdateService $orderUpdateService,
        ItemReadService $itemReadService,
        OrderDetailReadService $orderDetailReadService,
        OrderDetailCreateService $orderDetailCreateService,
        OrderDetailDeleteService $orderDetailDeleteService
    ) {
        $this->orderReadService = $orderReadService;
        $this->orderCreateService = $orderCreateService;
        $this->orderUpdateService = $orderUpdateService;
        $this->itemReadService = $itemReadService;
        $this->orderDetailReadService = $orderDetailReadService;
        $this->orderDetailCreateService = $orderDetailCreateService;
        $this->orderDetailDeleteService = $orderDetailDeleteService;
    }

    private function jsonResponse($message, $data = [], $status = 200)
    {
        return response()->json(array_merge(['message' => $message], $data), $status);
    }

    private function getAuthenticatedUser()
    {
        return Auth::user();
    }

    private function beginTransaction()
    {
        DB::beginTransaction();
    }

    private function commitTransaction()
    {
        DB::commit();
    }

    private function rollbackTransaction()
    {
        DB::rollBack();
    }

    /**
     * 注文リストへの登録
     * 注文リストに商品を追加する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|exists:items,item_code',
            'volume' => 'nullable|integer|min:1',
        ]);

        $auth = $this->getAuthenticatedUser();
        $item = $this->itemReadService->getByItemCode($request->input('item_code'));
        $volume = $request->input('volume', 1);

        try {
            // 新しいメソッドを使用して未発注の注文基本データを取得
            $order = $this->orderReadService->getUnorderedByUserIdAndSiteId($auth->id, $auth->site_id);

            // 持っていない場合は、注文基本データから一挙に作成
            if (!$order) {
                $orderDetail = $this->orderDetailCreateService->createOrderWithDetails($auth->site_id, $auth->id, $item, $volume);
            }
            // 持っている場合は、注文詳細データのみ作成
            else {
                $orderDetail = $this->orderDetailCreateService->create($order->id, $item, $volume);
            }

            return $this->jsonResponse(self::SUCCESS_MESSAGE, ['detail_code' => $orderDetail->detail_code], 201);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonResponse(self::UNEXPECTED_ERROR_MESSAGE, ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 注文詳細の指定削除
     * パラメータのItemCodeに一致する商品の注文を削除する。
     *
     * @param string $detailCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $item_code)
    {
        $auth = $this->getAuthenticatedUser();
        $item = $this->itemReadService->getByItemCode($item_code);

        if (!$item) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }

        $process = $this->orderDetailDeleteService->softDeleteItemFromUnorderedDetails($auth->id, $auth->site_id, $item->id);

        if (!$process || is_null($process)) {
            return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
        }

        return $this->jsonResponse(self::DELETE_SUCCESS_MESSAGE);
    }

    /**
     * 注文リストに登録されている商品の全削除
     * 注文リストに登録されている商品を全て削除する。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll()
    {
        $auth = $this->getAuthenticatedUser();

        $order = Order::where('user_id', $auth->id)
                      ->where('site_id', $auth->site_id)
                      ->whereNull('ordered_at')
                      ->first();

        if ($order) {
            DB::transaction(function () use ($order) {
                $order->orderDetails()->each(function ($orderDetail) {
                    $orderDetail->delete();
                });
            });

            return $this->jsonResponse(self::DELETE_ALL_SUCCESS_MESSAGE);
        }

        return $this->jsonResponse(self::NOT_FOUND_MESSAGE, [], 404);
    }

    /**
     * 注文実行
     * 注文リストに登録されている商品を発注する。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order()
    {
        $auth = $this->getAuthenticatedUser();

        $order = $this->orderUpdateService->updateOrderDate($auth->id, $auth->site_id);

        return $this->jsonResponse(self::ORDER_SUCCESS_MESSAGE, ['order_code' => $order->order_code]);
    }

}
