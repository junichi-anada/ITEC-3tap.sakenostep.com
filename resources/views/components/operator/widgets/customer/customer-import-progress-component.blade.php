<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">顧客データインポート</h2>
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

                <div class="overflow-y-auto max-h-[360px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    行番号
                                </th>
                                <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    取引先コード
                                </th>
                                <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    取引先名
                                </th>
                                <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ステータス
                                </th>
                                <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    処理日時
                                </th>
                                <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    エラーメッセージ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="importRecords">
                            @foreach($records as $record)
                            <tr>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->row_number }}
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->customer_code }}
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->customer_name }}
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->status_class }}">
                                        {{ $record->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->processed_at ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-sm text-red-600">
                                    {{ $record->error_message ?? '' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('operator.customer.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#F4CF41] hover:bg-[#E5C03C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F4CF41]">
                    顧客一覧に戻る
                </a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const taskCode = '{{ $task->task_code }}';
    const recordsContainer = document.getElementById('importRecords');

    if ('{{ $task->status }}' !== 'completed' && '{{ $task->status }}' !== 'failed') {
        // 3秒ごとにステータスを更新
        const intervalId = setInterval(function() {
            fetch(`/operator/customer/import/${taskCode}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // タスク情報の更新
                        document.getElementById('data-total-records').textContent = data.data.task.totalRecords;
                        document.getElementById('data-processed-records').textContent = data.data.task.processedRecords;
                        document.getElementById('data-success-records').textContent = data.data.task.successRecords;
                        document.getElementById('data-error-records').textContent = data.data.task.errorRecords;
                        // レコードの更新
                        const records = data.data.records;
                        let html = '';
                        records.forEach(record => {
                            html += `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${record.rowNumber}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${record.customerCode || ''}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${record.customerName || ''}
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
                                </tr>
                            `;
                        });
                        recordsContainer.innerHTML = html;

                        // タスクが完了したら更新を停止
                        if (data.data.task.status === 'completed' || data.data.task.status === 'failed') {
                            clearInterval(intervalId);
                            location.reload();
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 3000);
    }
});
</script>
@endpush

