<div class="border py-4 h-full">
    <div class="flex flex-col gap-y-4 h-full w-full px-4">
        <div class="flex flex-col gap-y-8 h-full">
            <div class="flex flex-row items-center gap-x-4">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">顧客情報</h3>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="name" class="w-full md:w-[5vw]">顧客名</label>
                {{ $order->customer->name }}
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="phone" class="w-full md:w-[5vw]">電話番号</label>
                {{ $order->customer->phone }}
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="phone2" class="w-full md:w-[5vw]">電話番号2</label>
                {{ $order->customer->phone2 }}
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="fax" class="w-full md:w-[5vw]">FAX番号</label>
                {{ $order->customer->fax }}
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="postal_code" class="w-full md:w-[5vw]">郵便番号</label>
                {{ $order->customer->postal_code }}
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="address" class="w-full md:w-[5vw]">住所</label>
                {{ $order->customer->address }}
            </div>

            <div class="flex justify-end mt-auto gap-x-4 items-center">
                <div>
                    <a href="{{ route('operator.customer.show', ['id' => $order->customer->id]) }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">顧客詳細へ</a>
                </div>
            </div>
        </div>
    </div>
</div>
