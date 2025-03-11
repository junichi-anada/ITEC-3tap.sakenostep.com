<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Services\Order\Dto\OrderUpdateDto;
use App\Services\Order\OrderDetailService;
use App\Services\Order\OrderExportService;
use App\Services\Order\OrderUpdateService;
use App\Services\Order\Queries\GetOrderListQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * @var OrderDetailService
     */
    private $orderDetailService;

    /**
     * @var OrderUpdateService
     */
    private $orderUpdateService;

    /**
     * @var OrderExportService
     */
    private $orderExportService;

    /**
     * コンストラクタ
     */
    public function __construct(
        OrderDetailService $orderDetailService,
        OrderUpdateService $orderUpdateService,
        OrderExportService $orderExportService
    ) {
        $this->orderDetailService = $orderDetailService;
        $this->orderUpdateService = $orderUpdateService;
        $this->orderExportService = $orderExportService;
    }

    /**
     * 注文一覧画面を表示
     */
    public function index(Request $request, GetOrderListQuery $query)
    {
        $orders = $query->execute($request->all());
        return view('operator.order.list', compact('orders'));
    }

    /**
     * 注文検索を実行
     */
    public function search(Request $request, GetOrderListQuery $query)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'nullable|string|max:100',
            'customer_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'order_date_from' => 'nullable|date',
            'order_date_to' => 'nullable|date|after_or_equal:order_date_from',
            'csv_export_status' => 'nullable|in:exported,not_exported',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $orders = $query->execute($request->all());

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->items(),
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage()
                ]
            ]
        ]);
    }

    /**
     * 注文詳細画面を表示
     */
    public function show($id)
    {
        $order = $this->orderDetailService->execute($id);
        if (!$order) {
            return redirect()->route('operator.order.index')
                ->with('error', $this->orderDetailService->getError());
        }

        return view('operator.order.show', compact('order'));
    }

    /**
     * 注文情報を更新（数量更新）
     */
    public function update(Request $request)
    {
        $dto = OrderUpdateDto::fromRequest($request->all());
        $result = $this->orderUpdateService->execute($dto);

        return response()->json([
            'success' => $result,
            'message' => $result ? '注文情報を更新しました。' : $this->orderUpdateService->getError()
        ]);
    }

    /**
     * 注文データをCSVファイルとして出力
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        try {
            // 未出力の注文を取得
            $orders = Order::with(['orderDetails' => function($query) {
                $query->whereNull('deleted_at');
            }, 'orderDetails.item', 'customer'])
            ->whereNotNull('ordered_at')
            ->whereNull('exported_at')
            ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => '出力対象の注文データが存在しません。'
                ], 404);
            }

            // リレーションチェック
            foreach ($orders as $order) {
                if (!$order->customer) {
                    Log::error("Customer not found for order ID: {$order->id}");
                    return response()->json([
                        'success' => false,
                        'message' => "注文ID: {$order->id} の顧客情報が見つかりません。"
                    ], 404);
                }

                foreach ($order->orderDetails as $detail) {
                    if (!$detail->item) {
                        Log::error("Item not found for order detail ID: {$detail->id}");
                        return response()->json([
                            'success' => false,
                            'message' => "注文ID: {$order->id} の商品情報が見つかりません。"
                        ], 404);
                    }
                }
            }

            // CSVファイル名を生成
            $filename = now()->format('YmdHi') . '_order.csv';

            $response = new StreamedResponse(function() use ($orders) {
                $handle = fopen('php://output', 'w');
                
                // SJISで出力する（BOMは不要）

                // ヘッダー行を書き込み
                // SJISエンコードしたヘッダー行も必要であれば以下のコメントを外して使用
                // $header = [
                //     '取引先区分',
                //     '伝票日付',
                //     '取引先コード',
                //     '納品先コード',
                //     '伝票行番区分',
                //     '商品コード',
                //     'JANコード',
                //     'ケース／バラ',
                //     '数量',
                //     '税区分',
                //     '単価',
                //     '金額',
                //     '相手先伝票番号',
                //     '相手先商品コード',
                //     '相手先商品名',
                //     '' // 最後にカンマを追加
                // ];
                // fputcsv($handle, array_map(function($value) {
                //     return mb_convert_encoding($value, 'SJIS', 'UTF-8');
                // }, $header));

                // データ行を書き込み
                foreach ($orders as $order) {
                    foreach ($order->orderDetails as $detail) {
                        try {
                            // JANコードを13桁に整形
                            $janCode = $detail->item->jan_code;
                            // if (empty($janCode)) {
                            //     $janCode = str_pad('', 13, '0');
                            // } else {
                            //     // 数値以外の文字を除去
                            //     $numbers = preg_replace('/[^0-9]/', '', $janCode);
                            //     // 13桁未満の場合は左側を0で埋める
                            //     if (strlen($numbers) < 13) {
                            //         $janCode = str_pad($numbers, 13, '0', STR_PAD_LEFT);
                            //     } else {
                            //         // 13桁を超える場合は左から13桁を使用
                            //         $janCode = substr($numbers, 0, 13);
                            //     }
                            // }

                            $row = [
                                '1',  // 取引先区分（固定値）
                                $order->ordered_at->format('Ymd'),  // 伝票日付
                                $order->customer->user_code,  // 取引先コード
                                '1',  // 納品先コード（固定値）
                                '1',  // 伝票行番区分（固定値）
                                $detail->item->item_code,  // 商品コード
                                $janCode,  // JANコード（13桁に整形）
                                ($detail->item->quantity_per_unit == 0 ? '0' : '1'),  // ケース／バラ
                                $detail->volume,  // 数量
                                '',  // 税区分（空白）
                                '',  // 単価（空白）
                                '',  // 金額（空白）
                                $order->order_code,  // 相手先伝票番号
                                $detail->item->item_code,  // 相手先商品コード
                                $detail->item->name,       // 相手先商品名
                                ''  // 最後にカンマを追加
                            ];
                            
                            // SJISで出力
                            fputcsv($handle, array_map(function($value) {
                                return mb_convert_encoding($value, 'SJIS', 'UTF-8');
                            }, $row));
                        } catch (\Exception $e) {
                            Log::error("CSV write error for order ID: {$order->id}, detail ID: {$detail->id}");
                            throw new \Exception("CSV書き出し中にエラーが発生しました。注文ID: {$order->id}");
                        }
                    }
                }

                fclose($handle);
            });

            // 出力前に注文のexported_atを更新（トランザクション処理を追加）
            try {
                \DB::beginTransaction();
                
                foreach ($orders as $order) {
                    $order->exported_at = now();
                    $order->save();
                    
                    // 更新が成功したことをログに記録
                    Log::info("Updated exported_at for order ID: {$order->id}");
                }
                
                \DB::commit();
                Log::info("Successfully updated exported_at for all orders: " . $orders->count() . " orders");
            } catch (\Exception $e) {
                \DB::rollBack();
                Log::error("Failed to update exported_at for orders: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'CSV出力処理中にエラーが発生しました。管理者にお問い合わせください。'
                ], 500);
            }

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;

        } catch (\Exception $e) {
            Log::error('CSV export failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'CSV出力に失敗しました。' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CSV出力の進捗状況を取得
     */
    public function exportProgress($taskCode)
    {
        $progress = $this->orderExportService->getProgress($taskCode);

        if ($progress === false) {
            return response()->json([
                'success' => false,
                'message' => $this->orderExportService->getError()
            ], 404);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    /**
     * CSV出力の状態を取得
     */
    public function exportStatus($taskCode)
    {
        $status = $this->orderExportService->getStatus($taskCode);

        if ($status === false) {
            return response()->json([
                'success' => false,
                'message' => $this->orderExportService->getError()
            ], 404);
        }

        if ($status['completed'] && isset($status['filePath'])) {
            return Response::download($status['filePath']);
        }

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    /**
     * 注文をCSV出力済みとしてマーク
     */
    public function markAsExported($id)
    {
        $result = $this->orderExportService->markAsExported($id);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'CSV出力済みとしてマークしました。' : $this->orderExportService->getError()
        ]);
    }
}
