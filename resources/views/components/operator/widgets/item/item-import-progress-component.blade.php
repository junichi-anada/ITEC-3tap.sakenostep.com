<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">商品データインポート</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusClass() }}">
                {{ $getStatusLabel() }}
            </span>
        </div>

        <div class="flex flex-col gap-y-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">インポート情報</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">タスクコード</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->task_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">アップロード日時</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->uploaded_at }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">インポート実行者</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->imported_by }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">完了日時</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $task->imported_at ?? '未完了' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">処理状況</h3>
                <div class="flex justify-between mb-4">
                    <div class="text-sm">
                        <span class="font-medium">総レコード数:</span>
                        <span id="data-total-records">{{ $task->total_records }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-medium">処理済み:</span>
                        <span id="data-processed-records">{{ $task->processed_records }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-medium">成功:</span>
                        <span class="text-green-600" id="data-success-records">{{ $task->success_records }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-medium">エラー:</span>
                        <span class="text-red-600" id="data-error-records">{{ $task->error_records }}</span>
                    </div>
                </div>

                <div class="overflow-y-auto max-h-[400px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    行番号
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    商品コード
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    商品名
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    カテゴリ
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ステータス
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    処理日時
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    エラーメッセージ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="importRecords">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('operator.item.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#F4CF41] hover:bg-[#E5C03C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F4CF41]">
                    商品一覧に戻る
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.record-new {
    animation: fadeInDown 0.5s ease-out forwards;
}
</style>
@endpush

@push('scripts')
<script>
class ImportProgressManager {
    constructor(taskCode) {
        this.taskCode = taskCode;
        this.recordsContainer = document.getElementById('importRecords');
        this.isProcessing = false;
        this.intervalId = null;
    }

    // レコードの表示を更新
    appendNewRecords(newRecords) {
        newRecords.forEach(record => {
            const tr = document.createElement('tr');
            tr.className = 'record-new';
            tr.innerHTML = this.createRecordHtml(record);
            // 新しいレコードを末尾に追加（時系列順）
            this.recordsContainer.appendChild(tr);

            // アニメーション終了後にクラスを削除
            setTimeout(() => {
                tr.classList.remove('record-new');
            }, 500);
        });
    }

    // レコードのHTML生成
    createRecordHtml(record) {
        return `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${record.rowNumber}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${record.itemCode || ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${record.itemName || ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${record.categoryName || ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${record.statusClass}">
                    ${record.statusLabel}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${record.processedAt || '-'}
            </td>
            <td class="px-6 py-4 text-sm text-red-600">
                ${record.errorMessage || ''}
            </td>
        `;
    }

    // タスク情報の更新
    updateTaskInfo(taskInfo) {
        document.getElementById('data-total-records').textContent = taskInfo.totalRecords;
        document.getElementById('data-processed-records').textContent = taskInfo.processedRecords;
        document.getElementById('data-success-records').textContent = taskInfo.successRecords;
        document.getElementById('data-error-records').textContent = taskInfo.errorRecords;
    }

    // ステータスの確認
    async checkStatus() {
        if (this.isProcessing) return;

        try {
            this.isProcessing = true;
            const response = await fetch(`/operator/item/import/${this.taskCode}/status`);
            const data = await response.json();

            if (data.success) {
                this.updateTaskInfo(data.data.task);

                // 新しいレコードがある場合のみ表示を更新
                if (data.data.newRecords && data.data.newRecords.length > 0) {
                    this.appendNewRecords(data.data.newRecords);
                    // 最新のレコードが見えるようにスクロール
                    const container = this.recordsContainer.parentElement;
                    container.scrollTop = container.scrollHeight;
                }

                // タスクが完了したら更新を停止
                if (data.data.task.status === 'completed' || data.data.task.status === 'failed') {
                    this.stopStatusCheck();
                    location.reload();
                }
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            this.isProcessing = false;
        }
    }

    // ステータスチェックの開始
    startStatusCheck() {
        this.intervalId = setInterval(() => this.checkStatus(), 3000);
    }

    // ステータスチェックの停止
    stopStatusCheck() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }
}

// DOMContentLoadedイベントでインスタンス化
document.addEventListener('DOMContentLoaded', function() {
    const taskCode = '{{ $task->task_code }}';
    const manager = new ImportProgressManager(taskCode);

    if ('{{ $task->status }}' !== 'completed' && '{{ $task->status }}' !== 'failed') {
        manager.startStatusCheck();
    }
});
</script>
@endpush
