<div class="flex flex-col gap-y-[0.43vw] h-full w-4/5 items-start" id="customer_order_data">
    <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">注文履歴</h3>

    @if ($orders->count() == 0)
        <p class="text-sm">注文履歴がありません</p>
    @else
        <div class="flex flex-col gap-y-[0.43vw] items-start w-full">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center py-[0.83vw] w-1/2 font-bold">注文日</div>
                    <div class="text-center py-[0.83vw] w-1/2 font-bold">注文番号</div>
                    <div class="text-center py-[0.83vw] w-1/2 font-bold">顧客名</div>
                </div>
                @if ($orders->count() > 0)
                    <div class="flex flex-col w-full">
                        @foreach ($orders as $order)
                            <a href="{{ route('operator.customer.show', ['id' => $customer->id]) }}">
                                <div class="flex px-4 gap-x-12 hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer w-full">
                                    <div class="text-center py-[0.62vw] w-1/2 text-sm">{{ $order->ordered_at }}</div>
                                    <div class="text-center py-[0.62vw] w-1/2 text-sm">{{ $order->order_code }}</div>
                                    <div class="text-center py-[0.62vw] w-1/2 text-sm">{{ $customer->name }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <!-- カスタムページネーションボタン -->
            <div class="pagination flex flex-row gap-x-4 justify-center w-full mt-4 ">
                @for ($i = 1; $i <= $orders->lastPage(); $i++)
                    <a href="{{ $orders->url($i) }}" class="block bg-[#F4CF41] px-[1.25vw] py-[0.83vw] rounded-md {{ $i == $orders->currentPage() ? 'border border-[#F4CF41]' : '' }}">
                        {{ $i }}
                        </a>
                    @endfor
            </div>
        </div>
    @endif
</div>
