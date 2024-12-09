<?php
/**
 * 顧客一覧表示ウィジェット
 *
 * @var \Illuminate\Pagination\LengthAwarePaginator $customers
 */
?>
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
    <div class="px-4 flex flex-col gap-y-4">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold  text-xl">顧客一覧</h2>
            <p class="text-sm">{{ $customers->total() }}件中 {{ $customers->count() }}件表示</p>
        </div>
        <div class="flex flex-col gap-y-4 items-start">
            <div class="w-full flex flex-col">
                <div class="flex">
                    <div class="text-center p-4 flex-1 font-bold">ログインID</div>
                    <div class="text-center p-4 flex-1 font-bold">顧客名</div>
                    <div class="text-center p-4 flex-1 font-bold">電話番号</div>
                    <div class="text-center p-4 w-1/4 font-bold">住所</div>
                    <div class="text-center p-4 flex-1 font-bold">初回利用日</div>
                    <div class="text-center p-4 flex-1 font-bold">最終利用日</div>
                    <div class="text-center p-4 flex-1 font-bold">LINE連携</div>
                    <div class="text-center p-4 flex-1 font-bold">利用状態</div>
                </div>
                @if ($customers->count() > 0)
                    <div class="flex flex-col  overflow-y-scroll h-[calc(100vh-20rem)]">
                        @foreach ($customers as $customer)
                            <a href="{{ route('operator.customer.show', ['id' => $customer->id]) }}">
                                <div class="flex hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer">
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->login_code }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->name }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        <div class="flex flex-col gap-y-1">
                                            @if($customer->phone)
                                                <span>{{ $customer->phone }}</span>
                                            @endif
                                            @if($customer->phone2)
                                                <span>{{ $customer->phone2 }}</span>
                                            @endif
                                            @if($customer->fax)
                                                <span>{{ $customer->fax }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-center p-4 w-1/4 text-sm">{{ $customer->address }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->first_login_at }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">{{ $customer->last_login_at }}</div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($customer->line_user_id)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                連携済み
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                未連携
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-center p-4 flex-1 text-sm">
                                        @if ($customer->deleted_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                登録解除
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                利用中
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
            {{ $customers->links() }}
        </div>
    </div>
</div>
