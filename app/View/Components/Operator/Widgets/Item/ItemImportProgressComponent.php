<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Item;

use Illuminate\View\Component;
use App\Models\ImportTask;
use App\Models\ImportTaskRecord;

class ItemImportProgressComponent extends Component
{
    public $task;
    public $operator;
    public $records;

    /**
     * Create a new component instance.
     *
     * @param ImportTask $task
     * @param mixed $operator
     * @return void
     */
    public function __construct($task, $operator)
    {
        $this->task = $task;
        $this->operator = $operator;
        $this->records = $this->processRecords();
    }

    /**
     * レコードデータを処理する
     *
     * @return \Illuminate\Support\Collection
     */
    private function processRecords()
    {
        return ImportTaskRecord::where('import_task_id', $this->task->id)
            ->orderBy('row_number')
            ->get()
            ->map(function ($record) {
                $data = json_decode($record->data, true);

                // データが配列でない場合は空配列を設定
                if (!is_array($data)) {
                    $data = [];
                }

                // 商品コード、商品名、カテゴリ情報を取得
                $itemCode = $data[0] ?? '';  // CSVの1列目を商品コードとして扱う
                $itemName = $data[2] ?? '';  // CSVの3列目を商品名として扱う
                $department = $data[1] ?? ''; // CSVの2列目を部門として扱う
                $categoryName = '';
                $customerCode = $data[3] ?? ''; // CSVの4列目を取引先コードとして扱う
                $customerName = $data[4] ?? ''; // CSVの5列目を取引先名として扱う

                // 部門情報からカテゴリ名を抽出
                if (strpos($department, ':') !== false) {
                    $parts = explode(':', $department);
                    $categoryName = trim($parts[1] ?? '');
                }

                // レコードに追加のプロパティを設定
                $record->item_code = $itemCode;
                $record->item_name = $itemName;
                $record->category_name = $categoryName;
                $record->customer_code = $customerCode;
                $record->customer_name = $customerName;
                $record->status_class = $this->getRecordStatusClass($record->status);
                $record->status_label = $this->getRecordStatusLabel($record->status);

                return $record;
            });
    }

    /**
     * インポート処理のステータスに応じたラベルクラスを取得
     *
     * @return string
     */
    public function getStatusClass()
    {
        return match($this->task->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'completed_with_errors' => 'bg-orange-100 text-orange-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * インポート処理のステータスに応じた日本語表示を取得
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return match($this->task->status) {
            'pending' => '待機中',
            'processing' => '処理中',
            'completed' => '完了',
            'completed_with_errors' => 'エラーあり',
            'failed' => '失敗',
            default => '不明',
        };
    }

    /**
     * レコードのステータスに応じたラベルクラスを取得
     *
     * @param string $status
     * @return string
     */
    private function getRecordStatusClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * レコードのステータスに応じた日本語表示を取得
     *
     * @param string $status
     * @return string
     */
    private function getRecordStatusLabel($status)
    {
        return match($status) {
            'pending' => '待機中',
            'processing' => '処理中',
            'completed' => '完了',
            'failed' => 'エラー',
            default => '不明',
        };
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.item.item-import-progress-component');
    }
}
