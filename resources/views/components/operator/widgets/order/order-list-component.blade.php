<?php
/**
 * 注文一覧表示ウィジェット
 *
 * @var \Illuminate\Pagination\LengthAwarePaginator $orders
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">注文一覧</h2>
            <p class="text-sm">{{ $orders->total() }}件中 {{ $orders->count() }}件表示</p>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">注文日</div>
                    <div class="text-center p-4 flex-1 font-bold">注文番号</div>
                    <div class="text-center p-4 flex-1 font-bold">顧客名</div>
                    <div class="text-center p-4 flex-1 font-bold">電話番号</div>
                    <div class="text-center p-4 w-1/4 font-bold">住所</div>
                    <div class="text-center p-4 flex-1 font-bold">注文状況</div>
                    <div class="text-center p-4 flex-1 font-bold">CSV書出</div>
                </div>
                @if ($orders->count() > 0)
                    <div class="flex flex-col overflow-y-scroll h-[calc(100vh-20rem)]">
                        @foreach ($orders as $order)
                            <a href="{{ route('operator.order.show', ['id' => $order->id]) }}">
                                <div class="flex hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer">
                                    <div class="text-center p-4 flex-1 text-sm">{{ optional($order->ordered_at)->format('Y-m-d') ?? '未設定' }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $order->order_code }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ optional($order->customer)->name ?? '未設定' }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        <div class="flex flex-col gap-y-1">
                                            @if(optional($order->customer)->phone)
                                                <span>{{ $order->customer->phone }}</span>
                                            @endif
                                            @if(optional($order->customer)->phone2)
                                                <span>{{ $order->customer->phone2 }}</span>
                                            @endif
                                            @if(optional($order->customer)->fax)
                                                <span>{{ $order->customer->fax }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-center p-4 w-1/4 text-sm break-words">{{ optional($order->customer)->address ?? '未設定' }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($order->status === '処理済')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $order->status }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $order->status }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($order->exported_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                書出済
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                未書出
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
