<div class="border py-4 h-full">
    <div class="flex flex-col gap-y-4 h-full w-full px-4">
        <div class="flex flex-row items-center gap-x-4">
            <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">注文履歴</h3>
        </div>
        <div class="overflow-y-auto h-[calc(100%-3rem)] scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            @if($orders->isEmpty())
                <p class="text-gray-500 text-center py-8">注文履歴がありません</p>
            @else
                <table class="min-w-full border-separate border-spacing-0">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                発注日
                            </th>
                            <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                注文コード
                            </th>
                            <th class="bg-gray-50 px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                エクスポート状態
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $order->created_at->format('Y/m/d') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap font-medium">
                                    {{ $order->order_code }}
                                </td>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    @if($order->is_exported)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                            出力済
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                            未出力
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
