<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Item;

use Illuminate\View\Component;
use App\Models\ImportTask;
use App\Models\ImportTaskRecord;

class ItemImportProgressComponent extends Component
{
    public ImportTask $task;
    public $records;

    /**
     * コンポーネントを作成
     *
     * @param ImportTask $task インポートタスク
     * @return void
     */
    public function __construct(ImportTask $task)
    {
        $this->task = $task;
        $this->records = $this->getRecords();
    }

    /**
     * コンポーネントを描画
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.operator.widgets.item.item-import-progress-component');
    }

    /**
     * タスクのステータスラベルを取得
     */
    public function getStatusLabel(): string
    {
        return match($this->task->status) {
            'waiting' => '待機中',
            'processing' => '処理中',
            'completed' => '完了',
            'failed' => 'エラー',
            default => '不明'
        };
    }

    /**
     * タスクのステータスに応じたクラスを取得
     */
    public function getStatusClass(): string
    {
        return match($this->task->status) {
            'waiting' => 'bg-gray-100 text-gray-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * レコードのステータスに応じたラベルクラスを取得
     *
     * @param string $status ステータス
     * @return string
     */
    private function getRecordStatusClass($status)
    {
        return match($status) {
            ImportTaskRecord::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            ImportTaskRecord::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            ImportTaskRecord::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            ImportTaskRecord::STATUS_FAILED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * レコードのステータスに応じたラベルテキストを取得
     *
     * @param string $status ステータス
     * @return string
     */
    private function getRecordStatusLabel($status)
    {
        return match($status) {
            ImportTaskRecord::STATUS_PENDING => '待機中',
            ImportTaskRecord::STATUS_PROCESSING => '処理中',
            ImportTaskRecord::STATUS_COMPLETED => '完了',
            ImportTaskRecord::STATUS_FAILED => '失敗',
            default => '不明',
        };
    }

    /**
     * インポートレコードを取得
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRecords()
    {
        return ImportTaskRecord::where('import_task_id', $this->task->id)
            ->orderBy('row_number', 'asc')
            ->get()
            ->map(function ($record) {
                $data = json_decode($record->data, true);

                // 商品コード、商品名、カテゴリ情報を取得
                $itemCode = $data[0] ?? '';  // CSVの1列目を商品コードとして扱う
                $department = $data[1] ?? ''; // CSVの2列目を部門として扱う
                $itemName = $data[2] ?? '';  // CSVの3列目を商品名として扱う
                $categoryName = '';

                // 部門情報からカテゴリ名を抽出
                if (strpos($department, ':') !== false) {
                    $parts = explode(':', $department);
                    $categoryName = trim($parts[1] ?? '');
                }

                // レコードに追加のプロパティを設定
                $record->item_code = $itemCode;
                $record->item_name = $itemName;
                $record->category_name = $categoryName;
                $record->status_class = $this->getRecordStatusClass($record->status);
                $record->status_label = $this->getRecordStatusLabel($record->status);

                return $record;
            });
    }
}
