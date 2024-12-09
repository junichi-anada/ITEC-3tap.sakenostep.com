<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Operator;
use App\Models\Site;
use App\Services\Item\ItemRegistrationService;
use App\Services\Item\ItemDeleteService;
use App\Services\Item\ItemUpdateService;
use App\Services\Item\ItemImportService;
use App\Services\Item\Queries\GetItemListQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    protected $itemImportService;

    public function __construct(ItemImportService $itemImportService)
    {
        $this->itemImportService = $itemImportService;
    }

    /**
     * 商品一覧ページ
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, GetItemListQuery $query)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        $searchParams = $request->only([
            'item_code',
            'name',
            'maker_name',
            'category_id',
            'published_at_from',
            'published_at_to',
            'from_source',
            'is_recommended'
        ]);

        $items = $query->execute($searchParams);
        $categories = ItemCategory::all();

        return view('operator.item.list', compact('operator', 'items', 'categories'));
    }

    /**
     * 商品登録ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        $categories = ItemCategory::all();
        $units = ItemUnit::all();
        $sites = Site::all();

        return view('operator.item.regist', compact('operator', 'categories', 'units', 'sites'));
    }

    /**
     * 商品登録処理
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ItemRegistrationService $itemRegistService)
    {
        // バリデーション
        $validator = validator($request->all(), [
            'item_code' => 'required|string|max:64|unique:items',
            'category_id' => 'required|exists:item_categories,id',
            'maker_name' => 'nullable|string|max:64',
            'name' => 'required|string|max:64',
            'description' => 'nullable|string',
            'is_recommended' => 'boolean',
            'published_at' => 'nullable|date',
        ], [
            'item_code.required' => '商品コードは必須です。',
            'item_code.max' => '商品コードは64文字以内で入力してください。',
            'item_code.unique' => 'この商品コードは既に使用されています。',
            'category_id.required' => 'カテゴリーは必須です。',
            'category_id.exists' => '選択されたカテゴリーは存在しません。',
            'maker_name.max' => 'メーカー名は64文字以内で入力してください。',
            'name.required' => '商品名は必須です。',
            'name.max' => '商品名は64文字以内で入力してください。',
            'is_recommended.boolean' => 'おすすめフラグは真偽値で指定してください。',
            'published_at.date' => '公開日時は正しい日付形式で入力してください。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = Auth::user();

        // from_sourceを設定
        $request->merge([
            'from_source' => 'MANUAL'
        ]);

        $result = $itemRegistService->registItem($request, $auth);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success', 'item_id' => $result['item_id']]);
        } else {
            return response()->json(['message' => 'error', 'reason' => $result['reason']], 500);
        }
    }

    /**
     * 商品詳細情報表示
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        $item = Item::with(['category', 'unit', 'site'])
                    ->findOrFail($id);

        return view('operator.item.show', compact('operator', 'item'));
    }

    /**
     * 商品情報編集ページ表示
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        $item = Item::findOrFail($id);
        $categories = ItemCategory::all();
        $units = ItemUnit::all();
        $sites = Site::all();

        return view('operator.item.edit', compact('operator', 'item', 'categories', 'units', 'sites'));
    }

    /**
     * 商品情報更新処理
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id, ItemUpdateService $itemUpdateService)
    {
        $auth = Auth::user();

        // バリデーション
        $validator = validator($request->all(), [
            'category_id' => 'required|exists:item_categories,id',
            'maker_name' => 'nullable|string|max:64',
            'name' => 'required|string|max:64',
            'description' => 'nullable|string',
            'is_recommended' => 'boolean',
            'published_at' => 'nullable|date',
        ], [
            'category_id.required' => 'カテゴリーは必須です。',
            'category_id.exists' => '選択されたカテゴリーは存在しません。',
            'maker_name.max' => 'メーカー名は64文字以内で入力してください。',
            'name.required' => '商品名は必須です。',
            'name.max' => '商品名は64文字以内で入力してください。',
            'is_recommended.boolean' => 'おすすめフラグは真偽値で指定してください。',
            'published_at.date' => '公開日時は正しい日付形式で入力してください。',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $result = $itemUpdateService->updateItem($id, $request->all());

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['message'] === 'success') {
            return redirect()->route('operator.item.show', $id)
                ->with('success', '商品情報を更新しました。');
        } else {
            return redirect()->route('operator.item.show', $id)
                ->with('error', '商品情報の更新に失敗しました。');
        }
    }

    /**
     * 商品情報削除処理
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id, ItemDeleteService $itemDeleteService)
    {
        $auth = Auth::user();
        $item = Item::findOrFail($id);

        $result = $itemDeleteService->deleteItem($item, $auth);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['message'] === 'success') {
            return redirect()->route('operator.item.index')
                ->with('success', '商品情報を削除しました。');
        } else {
            return redirect()->route('operator.item.show', $id)
                ->with('error', '商品情報の削除に失敗しました。');
        }
    }

    /**
     * 商品インポート処理
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // バリデーション
        $validator = validator($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
        ], [
            'file.required' => 'ファイルを選択してください。',
            'file.file' => '有効なファイルを選択してください。',
            'file.mimes' => 'CSV、TXT、XLS、XLSXファイルを選択してください。',
            'file.max' => 'ファイルサイズは10MB以下にしてください。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $auth = Auth::user();
            $file = $request->file('file');
            $task = $this->itemImportService->createImportTask(
                $file->getRealPath(),
                $auth->site_id,
                $auth->login_code
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'taskCode' => $task->task_code
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * インポート状態の確認
     *
     * @param string $taskCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function importStatus($taskCode)
    {
        try {
            $status = $this->itemImportService->getTaskStatus($taskCode);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'タスクが見つかりません'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
