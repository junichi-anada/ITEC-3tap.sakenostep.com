<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold  text-xl">顧客一覧</h2>
            <p class="text-sm">{{ $user_count }}件中 {{ $customers->count() }}件表示</p>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">お客様番号</div>
                    <div class="text-center p-4 flex-1 font-bold">顧客名</div>
                    <div class="text-center p-4 flex-1 font-bold">電話番号</div>
                    <div class="text-center p-4 w-1/4 font-bold">住所</div>
                    <div class="text-center p-4 flex-1 font-bold">初回注文日</div>
                    <div class="text-center p-4 flex-1 font-bold">最終注文日</div>
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
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->first_order_date }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->last_order_date }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
