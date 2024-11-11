<?php
/**
 * 顧客一覧表示ウィジェット
 * 
 * @var \App\Models\Customer[]|\Illuminate\Database\Eloquent\Collection $customers
 * @var int $customer_count
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold  text-xl">商品一覧</h2>
            <p class="text-sm">{{ $customer_count }}件中 {{ $customers->count() }}件表示</p>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">商品番号</div>
                    <div class="text-center p-4 flex-1 font-bold">カテゴリ</div>
                    <div class="text-center p-4 flex-1 font-bold">メーカー</div>
                    <div class="text-center p-4 w-1/4 font-bold">商品名</div>
                    <div class="text-center p-4 flex-1 font-bold">リスト表示</div>
                </div>
                @if ($customers->count() > 0)
                    <div class="flex flex-col">
                        @foreach ($customers as $customer)
                            <a href="{{ route('operator.customer.show', ['id' => $customer->id]) }}">
                                <div class="flex hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer">
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->login_code }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->name }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->phone }}</div>
                                    <div class="text-center p-4 w-1/4 text-sm">{{ $customer->address }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->first_login_at }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
