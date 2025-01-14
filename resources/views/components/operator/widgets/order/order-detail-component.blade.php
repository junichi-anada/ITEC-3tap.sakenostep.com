<div class="border py-4 h-full">
    <div class="flex flex-col gap-y-4 h-full w-full px-4">
        <div class="flex flex-col gap-y-4 h-full">
            <div class="flex flex-row items-center gap-x-4">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">注文情報</h3>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="order_number" class="w-full md:w-[8vw]">注文番号</label>
                <input type="text" name="order_number" id="order_number" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $order->order_code }}" readonly>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="order_date" class="w-full md:w-[8vw]">注文日</label>
                <input type="text" name="order_date" id="order_date" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $order->ordered_at ? $order->ordered_at->format('Y-m-d') : '' }}" readonly>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="csv_export_status" class="w-full md:w-[8vw]">CSV書出</label>
                <div class="flex items-center">
                    @if ($order->csv_exported_at)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-md">書出済 ({{ $order->csv_exported_at->format('Y-m-d H:i:s') }})</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md">未書出</span>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex-1 overflow-hidden">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2 mb-4">注文明細</h3>
                <form id="order_detail_form" class="h-full">
                    @csrf
                    <input type="hidden" name="order_code" value="{{ $order->order_number }}">
                    <div class="overflow-auto h-full">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        商品コード
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        商品名
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        容量
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ケース入数
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        数量
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->orderDetails as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $detail->item->code }}
                                            <input type="hidden" name="details[{{ $loop->index }}][item_code]" value="{{ $detail->item->code }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $detail->item->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $detail->item->capacity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $detail->item->quantity_per_unit }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <input type="number"
                                                name="details[{{ $loop->index }}][quantity]"
                                                value="{{ $detail->quantity }}"
                                                class="w-24 text-center border border-gray-300 rounded-md py-1"
                                                min="1">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <div class="flex justify-end mt-4 gap-x-4 items-center">
                <div>
                    <button type="button" id="update_quantities" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md mr-2">数量を更新</button>
                    <a href="{{ route('operator.order.index') }}" class="bg-gray-500 text-white px-8 py-1 rounded-md">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
</div>
