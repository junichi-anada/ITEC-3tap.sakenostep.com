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
    public function progress($taskCode)
    {
        $auth = Auth::user();
        $operator = Operator::where('id', $auth->entity_id)->first();

        $task = ImportTask::where('task_code', $taskCode)
            ->where('data_type', ImportTask::DATA_TYPE_ITEM)
            ->firstOrFail();

        return view('operator.item.import-progress', compact('operator', 'task'));
    }
}
