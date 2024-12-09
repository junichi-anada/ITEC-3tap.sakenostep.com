<?php
/**
 * 顧客注文履歴表示ウィジェット
 *
 * @var \Illuminate\Pagination\LengthAwarePaginator $orders
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold text-xl">注文履歴</h2>
            <p class="text-sm">{{ $orders->total() }}件中 {{ $orders->count() }}件表示</p>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">注文番号</div>
                    <div class="text-center p-4 flex-1 font-bold">注文日時</div>
                    <div class="text-center p-4 flex-1 font-bold">合計金額</div>
                    <div class="text-center p-4 flex-1 font-bold">ステータス</div>
                </div>
                @if ($orders->count() > 0)
                    <div class="flex flex-col">
                        @foreach ($orders as $order)
                            <div class="flex hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer">
                                <div class="text-center p-4 flex-1 text-sm">{{ $order->order_code }}</div>
                                <div class="text-center p-4 flex-1 text-sm">{{ $order->ordered_at }}</div>
                                <div class="text-center p-4 flex-1 text-sm">¥{{ number_format($order->total_price) }}</div>
                                <div class="text-center p-4 flex-1 text-sm">
                                    @if ($order->processed_at)
                                        処理済み
                                    @else
                                        未処理
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center p-4">
                        注文履歴がありません
                    </div>
                @endif
            </div>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
