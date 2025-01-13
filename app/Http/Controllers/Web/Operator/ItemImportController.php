<?php

namespace App\Http\Controllers\Web\Operator;

use App\Http\Controllers\Controller;
use App\Models\ImportTask;
use App\Models\Operator;
use Illuminate\Support\Facades\Auth;

class ItemImportController extends Controller
{
    /**
     * インポート進捗表示ページ
     *
     * @param string $taskCode
     * @return \Illuminate\View\View
     */
    public function progress(string $taskCode)
    {
        $auth = Auth::user();
        
        // タスク情報を取得
        $task = ImportTask::where('task_code', $taskCode)
            ->where('site_id', $auth->site_id)
            ->firstOrFail();

        return view('operator.item.import.progress', [
            'task' => $task,
            'operator' => $auth
        ]);
    }
}
