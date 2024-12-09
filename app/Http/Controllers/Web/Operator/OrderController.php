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
        return view('operator.order.index', compact('orders'));
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
     * 注文データをCSVファイルとして出力（非同期処理開始）
     */
    public function export(Request $request)
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

        $taskCode = $this->orderExportService->startExport($request->all());

        if (!$taskCode) {
            return response()->json([
                'success' => false,
                'message' => $this->orderExportService->getError()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'taskCode' => $taskCode,
            'message' => 'CSV出力処理を開始しました。'
        ]);
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
