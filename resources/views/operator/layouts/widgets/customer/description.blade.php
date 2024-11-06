<form action="{{ route('operator.customer.regist') }}" method="post" id="customer_form">
    @csrf
    <div class="flex flex-row border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
        <div class="px-4 flex flex-col gap-y-4 h-full w-full">
            <div class="flex flex-row items-center justify-between pb-2 border-b">
                <h2 class="font-bold  text-xl">顧客詳細</h2>
            </div>
            <div class="flex flex-row gap-24 w-full">
                <div class="flex flex-col gap-y-[1.25vw] h-full w-1/2" id="customer_regist_data">
                    <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">顧客情報</h3>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="user_code" class="w-full md:w-[5vw]">お客様番号</label>
                        <input type="text" name="user_code" id="user_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5 text-gray-300 bg-gray-100" value="{{ $customer->login_code }}" placeholder="お客様番号" readonly>
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="name" class="w-full md:w-[5vw]">顧客名</label>
                        <input type="text" name="name" id="name" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $customer->name }}" placeholder="顧客名">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="phone" class="w-full md:w-[5vw]">電話番号</label>
                        <input type="text" name="phone" id="phone" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $customer->phone }}" placeholder="電話番号">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="postal_code" class="w-full md:w-[5vw]">郵便番号</label>
                        <input type="text" name="postal_code" id="postal_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $customer->postal_code }}" placeholder="郵便番号">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="address" class="w-full md:w-[5vw]">住所</label>
                        <input type="text" name="address" id="address" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $customer->address }}" placeholder="青森県十和田市">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="address" class="w-full md:w-[5vw]">初回利用日</label>
                        <input type="text" name="date" id="address" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5 text-gray-400 bg-gray-100" value="{{ $customer->first_login_at ?? '' }}" readonly>
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-[0.83vw] items-center">
                        <label for="address" class="w-full md:w-[5vw]">最終利用日</label>
                        <input type="text" name="date" id="address" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5 text-gray-400 bg-gray-100" value="{{ $customer->last_login_at ?? '' }}" readonly>
                    </div>
                    @include('operator.layouts.widgets.customer.line_message')
                </div>
                    @include('operator.layouts.widgets.order.list', ['orders' => $orders])
            </div>
            <div class="flex justify-end mt-auto gap-x-8 items-center">
                <div>
                    <a class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer" id="customer_delete">顧客削除</a>
                </div>
                <div>
                    <a class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer" id="customer_update">変更適用</a>
                </div>
                <div>
                    <a href="{{ route('operator.customer.list') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
</form>



