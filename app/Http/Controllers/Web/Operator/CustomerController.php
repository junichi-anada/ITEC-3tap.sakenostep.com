<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\Authenticate;
use App\Models\ImportTask;
use App\Models\Operator;
use App\Models\Order;
use App\Models\User;
use App\Services\Operator\Customer\Read\Component\List\CustomerListService as CustomerListService;
use App\Services\Operator\Customer\RegistService as CustomerRegistService;
use App\Services\Operator\Customer\DeleteService as CustomerDeleteService;
use App\Services\Operator\Customer\UpdateService as CustomerUpdateService;
use App\Services\Operator\Customer\Read\Component\List\UserSearchService as UserSearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// 酒のステップ専用のインポートサービスクラス
use App\Services\Operator\Customer\Import\Special\SakenoStep\SakenoStepImportService;
use App\Services\Operator\Customer\Import\GeneralImportService;
use App\Services\Operator\Customer\Import\Special\SakenoStep\SakenoStepDataFormatterService;
use App\Services\Operator\Customer\Import\GeneralDataFormatterService;
use App\Services\Operator\Customer\Import\Special\SakenoStep\SakenoStepValidationService;
use App\Services\Operator\Customer\Import\GeneralValidationService;

use App\Jobs\ProcessCustomerImport;

class CustomerController extends Controller
{
    private $customerImportService;
    private $customerDataFormatterService;
    private $customerValidationService;

    public function __construct(SakenoStepImportService $customerImportService, SakenoStepDataFormatterService $customerDataFormatterService, SakenoStepValidationService $customerValidationService)
    {
        $this->customerImportService = $customerImportService;
        $this->customerDataFormatterService = $customerDataFormatterService;
        $this->customerValidationService = $customerValidationService;
    }


    /**
     * 顧客一覧ページ
     *
     * @return \Illuminate\View\View
     */
    public function index(CustomerListService $customerListService)
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 検索条件なしで顧客一覧を取得
        $customers = $customerListService->getList();

        return view('operator.customer.list', compact('operator', 'customers'));
    }

    /**
     * 顧客手動登録ページ表示
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // お客様番号をUUIDで生成(仮)
        $customer_code = Str::uuid();

        return view('operator.customer.regist', compact('operator', 'customer_code'));
    }

    /**
     * 顧客手動登録処理
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function store(Request $request, CustomerRegistService $customerRegistService)
    {
        // バリデーション
        $request->validate([
            'user_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $auth = Auth::user();

        $result = $customerRegistService->registCustomer($request, $auth);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success', 'login_code' => $result['login_code'], 'password' => $result['password']]);
        } else {
            return response()->json(['message' => $result['message'], 'reason' => $result['reason']], 500);
        }
    }

    /**
     * 顧客データ1件取得
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // userとauthenticatesを結合して取得(未削除のデータ)
        $customer = User::where('users.id', $request->id)
            ->join('authenticates', 'users.id', '=', 'authenticates.entity_id')
            ->select('users.*', 'authenticates.login_code', 'authenticates.created_at as first_login_at', 'authenticates.updated_at as last_login_at')
            ->whereNull('users.deleted_at')
            ->where('authenticates.entity_type', User::class)
            ->first();

        if (!$customer) {
            $error_message = '該当する顧客情報が見つかりませんでした。';
            return redirect()->route('operator.customer.error')
                ->with('error_message', $error_message)
                ->with('operator', $operator);
        }

        // 注文履歴をページネーション
        $orders = Order::where('site_id', $auth->site_id)
            ->where('user_id', $customer->id)
            ->orderBy('ordered_at', 'desc')
            ->paginate(10);

        return view('operator.customer.description', compact('customer', 'operator', 'orders'));
    }

    /**
     * 顧客データ1件削除
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, CustomerDeleteService $customerDeleteService, $id)
    {
        $auth = Auth::user();

        // バリデーション
        // $request->validate([
        //     'user_code' => 'required|string|max:255',
        // ]);

        $result = $customerDeleteService->deleteCustomer($id);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => $result['reason']], 500);
        }
    }

    /**
     * 検索
     *
     * @return \Illuminate\View\View
     */
    public function search(Request $request, UserSearchService $userSearchService)
    {
        // バリデーション
        $request->validate([
            'customer_code' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'first_login_date_from' => 'nullable|date',
            'first_login_date_to' => 'nullable|date',
            'last_login_date_from' => 'nullable|date',
            'last_login_date_to' => 'nullable|date',
        ]);

        $auth = Auth::user();

        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        // 検索条件を取得
        $search = [
            'customer_code' => $request->customer_code,
            'customer_name' => $request->customer_name,
            'customer_address' => $request->customer_address,
            'customer_phone' => $request->customer_phone,
            'first_login_date_from' => $request->first_login_date_from,
            'first_login_date_to' => $request->first_login_date_to,
            'last_login_date_from' => $request->last_login_date_from,
            'last_login_date_to' => $request->last_login_date_to,
        ];

        // 検索条件を元に顧客一覧を取得
        $customers = $userSearchService->searchList($search);

        return view('operator.customer.list', compact('operator', 'customers'));
    }

    /**
     * 顧客データ��新
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CustomerUpdateService $customerUpdateService, $id)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $data = $request->only(['name', 'postal_code', 'phone', 'address']);

        $result = $customerUpdateService->updateCustomer($id, $data);

        if ($result['message'] === 'success') {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => $result['reason']], 500);
        }
    }

    /**
     * アップロードファイル受取処理
     * 1. アップロードファイルを受け取り、保存する
     * 2. importTaskに登録する
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // スクリプトの実行時間制限を300秒（5分）に設定
        set_time_limit(300);

        // 許可する拡張子
        $accept_extension = ['csv', 'txt', 'xlsx', 'xls'];

        // バリデーション
        $request->validate([
            'customerFile' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/vnd.ms-excel',
        ]);

        // サイトごとのサービスクラスを取得
        $auth = Auth::user();
        // if($auth->site_id == 1) {
        //     $customerImportService = new SakenoStepCustomerImportService();
        // } else {
        // $customerImportService = new CustomerImportService();
        // }

        $file = $request->file('customerFile');

        // ファイルの保存先の絶対パスを取得する。
        $file_path = $file->storeAs('customer', $file->getClientOriginalName());
        $file_path = storage_path('app/' . $file_path);
        $extension = $file->getClientOriginalExtension();

        Log::info("file_path:".$file_path);
        if (!in_array($extension, $accept_extension)) {
            return response()->json(['message' => 'error', 'reason' => 'Invalid file extension'], 400);
        }

        $importProcess =$this->customerImportService->import($file_path);

        // タスク作成
        $importTask = $this->customerImportService->createTask($file_path);
        if ($importTask === null) {
            return response()->json(['message' => 'error', 'reason' => 'Failed to create import task'], 500);
        }

        // 成功した場合のレスポンス
        return response()->json(['message' => 'success', 'task_code' => $importTask->task_code], 200);

        // 失敗した場合のレスポンス
        return response()->json(['message' => 'error', 'reason' => 'Some error message'], 500);
    }

    /**
     * アップロードステータス
     *
     * @return \Illuminate\View\View
     */
    public function status(Request $request)
    {
        // バリデーション
        $request->validate([
            'task_code' => 'required|string|max:255',
        ]);

        $auth = Auth::user();
        // if($auth->site_id == 1) {
        //     $customerImportService = new SakenoStepImportService();
        // } else {
            // $customerImportService = new CustomerImportService();
        // }
        // auth->entity_idでログインしているオペレーターの名前Operatorから取得
        $operator = Operator::where('id', $auth->entity_id)->first();

        $taskCode = $request->task_code;

        // タスクを取得
        $importTask = ImportTask::where('task_code', $taskCode)->first();
        if (!$importTask) {
            return response()->json(['message' => 'error', 'reason' => 'Task not found'], 404);
        }

        // ファイルの存在確認
        if (!file_exists($importTask->file_path)) {
            return response()->json(['message' => 'error', 'reason' => 'File does not exist.'], 400);
        }

        $file = new \SplFileInfo($importTask->file_path);
        $extension = $file->getExtension();

        $rows = $this->processFileContent($file, $extension);

        // ファイルの内容をチェック
        $validationErrors = $this->customerValidationService->validateFileContent($rows);
        if (!empty($validationErrors)) {
            return response()->json(['message' => 'error', 'reason' => 'The content of the data file is invalid.', 'errors' => $validationErrors], 400);
        }

        // formatDataメソッドで整形する
        $formattedData = $this->customerDataFormatterService->formatData($rows);
        if (empty($formattedData)) {
            return response()->json(['message' => 'error', 'reason' => 'Formatting error'], 400);
        }

        // Log::info("formattedData:".json_encode($formattedData));

        $amount = "";
        $amount = count($formattedData);

        // ファイルパスから処理をここで行う
        try {
            $result = $this->customerImportService->importToDatabase($formattedData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'reason' => 'An error occurred during import to the database.'], 500);
        }

        return view('operator.customer.upload.status', compact('operator', 'amount'));
    }

    /**
     * ファイルの内容を配列化
     *
     * @param [type] $file
     * @param [type] $extension
     * @return array
     */
    private function processFileContent($file, $extension): array
    {
        // CSVファイル、テキストファイルの場合
        if (in_array($extension, ['csv', 'txt'])) {
            $fileContent = file_get_contents($file->getRealPath());

            // カンマ区切りでデータを配列に変換
            return array_map('str_getcsv', explode("\n", $fileContent));
        }

        // Excelファイルの場合
        if (in_array($extension, ['xlsx', 'xls'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];
            $headers = []; // タイトル行を格納する配列
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cellIndex => $cell) {
                    if ($rowIndex === 1) {
                        // 先頭行の場合、ヘッダーとして設定
                        $headers[$cellIndex] = $cell->getValue();
                    } else {
                        // データ行の場合、ヘッダーをキーとした連想配列に変換
                        $rowData[$headers[$cellIndex]] = $cell->getValue();
                    }
                }
                if ($rowIndex !== 1) { // 先頭行を除外
                    $rows[] = $rowData;
                }
            }
            return $rows;
        }
    }
}
