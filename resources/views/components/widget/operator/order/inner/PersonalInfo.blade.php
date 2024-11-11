<form id="customer_form">
    @csrf
    <input type="hidden" name=id value="{{ $customer->id }}">
    <div class="flex flex-col gap-y-[1.25vw] h-full">
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
    </div>
</form>
