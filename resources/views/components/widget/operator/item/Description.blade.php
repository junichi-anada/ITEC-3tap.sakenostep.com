<div class="flex flex-row border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
    <div class="px-4 flex flex-col gap-y-4 h-full w-full">
        <div class="flex flex-row items-center justify-between pb-2 border-b">
            <h2 class="font-bold  text-xl">商品詳細</h2>
        </div>
        <div class="flex flex-row gap-24 w-full">
            <div class="flex flex-col w-1/2 gap-y-4">
                <x-widget.operator.customer.inner.personal-info-component :customer="$customer" />
                <x-widget.operator.customer.inner.line-personal-message-component />
            </div>
            <x-widget.operator.order.inner.list-by-customer-component :orders="$orders" :customer="$customer" />
        </div>
        <div class="flex justify-end mt-auto gap-x-8 items-center">
            <div>
                <a class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer" id="customer_update">変更適用</a>
            </div>
            <div>
                <a href="{{ route('operator.customer.index') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">一覧に戻る</a>
            </div>
        </div>
    </div>
</div>
