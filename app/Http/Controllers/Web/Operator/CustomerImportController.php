<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\ImportTask;
use App\Models\Operator;
use Illuminate\Support\Facades\Auth;

class CustomerImportController extends Controller
{
    /**
     * インポート処理の進捗確認画面を表示
     *
     * @param string $taskCode タスクコード
     * @return \Illuminate\View\View
     */
    public function progress(string $taskCode)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();
        
        // タスク情報を取得
        $task = ImportTask::where('task_code', $taskCode)
            ->where('site_id', $auth->site_id)
            ->firstOrFail();

        return view('operator.customer.import-progress', [
            'operator' => $operator,
            'task' => $task
        ]);
    }
}
