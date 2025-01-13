@php
use App\Models\ImportTaskRecord;
@endphp

<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">商品データインポート</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getStatusClass() }}">
                {{ $getStatusLabel() }}
            </span>
        </div>

        <div class="flex flex-col gap-y-4">
            <!-- インポート情報 -->
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

            <!-- 処理状況 -->
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
            </div>

            <!-- エラーレコード一覧 -->
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">処理エラー一覧</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">行番号</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商品コード</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">エラー内容</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="importRecords">
                            @foreach($task->records()->where('status', ImportTaskRecord::STATUS_FAILED)
                                ->orderBy('row_number')
                                ->get() as $record)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->row_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $data = json_decode($record->data, true);
                                        echo $data[0] ?? '';
                                    @endphp
                                </td>
                                <td class="px-6 py-4 text-sm text-red-600">
                                    {{ $record->error_message }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const taskCode = '{{ $task->task_code }}';
    const recordsContainer = document.getElementById('importRecords');
    let isCompleted = false;

    const updateStatus = () => {
        if (isCompleted) return;

        fetch(`/operator/item/import/${taskCode}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 進捗情報の更新
                    document.getElementById('data-total-records').textContent = 
                        Number(data.data.task.totalRecords).toLocaleString();
                    document.getElementById('data-processed-records').textContent = 
                        Number(data.data.task.processedRecords).toLocaleString();
                    document.getElementById('data-success-records').textContent = 
                        Number(data.data.task.successRecords).toLocaleString();
                    document.getElementById('data-error-records').textContent = 
                        Number(data.data.task.errorRecords).toLocaleString();

                    // エラーレコードのみを表示
                    const errorRecords = data.data.records.filter(record => record.status === 'failed');
                    let html = '';
                    errorRecords.forEach(record => {
                        const data = JSON.parse(record.data);
                        html += `
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${record.rowNumber}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${data[0] || ''}
                                </td>
                                <td class="px-6 py-4 text-sm text-red-600">
                                    ${record.errorMessage || ''}
                                </td>
                            </tr>
                        `;
                    });
                    recordsContainer.innerHTML = html;

                    // タスクが完了したかチェック
                    if (data.data.task.status === 'completed' || data.data.task.status === 'failed') {
                        isCompleted = true;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    };

    // 初回実行
    updateStatus();

    // 3秒ごとに更新（タスクが完了するまで）
    const intervalId = setInterval(() => {
        if (isCompleted) {
            clearInterval(intervalId);
            return;
        }
        updateStatus();
    }, 3000);
});
</script>
@endpush
