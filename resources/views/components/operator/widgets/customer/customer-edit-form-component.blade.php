<form id="customer_form" method="POST" action="{{ route('operator.customer.update', $user->id) }}">
    @csrf
    @method('PUT')
    <div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
        <div class="px-4 flex flex-col gap-y-4 h-full w-full">
            <div class="flex flex-row items-center justify-between pb-2 border-b">
                <h2 class="font-bold text-xl">顧客情報編集</h2>
            </div>
            <div class="flex flex-col gap-y-8 h-full">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">顧客情報</h3>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="login_code" class="w-full md:w-[8vw] font-semibold">ログインID</label>
                    <input type="text" name="login_code" id="login_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $authenticate->login_code }}" readonly>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="name" class="w-full md:w-[8vw] font-semibold">顧客名</label>
                    <input type="text" name="name" id="name" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->name }}" required>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="phone" class="w-full md:w-[8vw] font-semibold">電話番号</label>
                    <input type="text" name="phone" id="phone" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->phone }}" required>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="phone2" class="w-full md:w-[8vw] font-semibold">電話番号2</label>
                    <input type="text" name="phone2" id="phone2" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->phone2 }}" placeholder="電話番号2（任意）">
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="fax" class="w-full md:w-[8vw] font-semibold">FAX番号</label>
                    <input type="text" name="fax" id="fax" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->fax }}" placeholder="FAX番号（任意）">
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="postal_code" class="w-full md:w-[8vw] font-semibold">郵便番号</label>
                    <input type="text" name="postal_code" id="postal_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->postal_code }}" required>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="address" class="w-full md:w-[8vw] font-semibold">住所</label>
                    <input type="text" name="address" id="address" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $user->address }}" required>
                </div>

                <div class="flex justify-end mt-auto gap-x-4 items-center">
                    <button type="submit" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">更新</button>
                    <a href="{{ route('operator.customer.show', $user->id) }}" class="bg-gray-500 text-white px-8 py-1 rounded-md">戻る</a>
                </div>
            </div>
        </div>
    </div>
</form>
