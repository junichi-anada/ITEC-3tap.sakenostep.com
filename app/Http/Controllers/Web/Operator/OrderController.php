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
use Illuminate\Http\StreamedResponse;
use Illuminate\Support\Facades\Log;

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
    public function export()
    {
        try {
            // 未出力の注文を取得
            $orders = Order::with(['orderDetails' => function($query) {
                $query->whereNull('deleted_at');
            }])
            ->whereNull('exported_at')
            ->get();

            // CSVファイル名を生成
            $filename = now()->format('YmdHi') . '.csv';

            $response = new StreamedResponse(function() use ($orders) {
                $handle = fopen('php://output', 'w');
                
                // BOMを付与
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                // ヘッダー行を書き込み
                fputcsv($handle, [
                    '注文番号',
                    '注文日時',
                    '顧客名',
                    '商品コード',
                    '商品名',
                    '数量',
                    // 必要に応じて他のカラムを追加
                ]);

                // データ行を書き込み
                foreach ($orders as $order) {
                    foreach ($order->orderDetails as $detail) {
                        fputcsv($handle, [
                            $order->order_number,
                            $order->created_at->format('Y-m-d H:i:s'),
                            $order->customer->name,
                            $detail->item->item_code,
                            $detail->item->name,
                            $detail->quantity,
                            // 必要に応じて他のカラムを追加
                        ]);
                    }
                }

                fclose($handle);
            });

            // 出力が完了した注文のexported_atを更新
            $orders->each(function ($order) {
                $order->exported_at = now();
                $order->save();
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;

        } catch (\Exception $e) {
            Log::error('CSV export failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'CSV出力に失敗しました。'
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
