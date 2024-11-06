{{-- 顧客検索 --}}
<form action="{{ route('operator.customer.search') }}" method="get">
@csrf
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">顧客検索</h2>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_code" class="w-full md:w-1/3">お客様番号</label>
                <input type="text" name="customer_code" id="customer_code" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="お客様番号" value="{{ old('customer_code', request('customer_code')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_name" class="w-full md:w-1/3">顧客名</label>
                <input type="text" name="customer_name" id="customer_name" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="顧客名" value="{{ old('customer_name', request('customer_name')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_address" class="w-full md:w-1/3">住所</label>
                <input type="text" name="customer_address" id="address" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="青森県十和田市" value="{{ old('customer_address', request('customer_address')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_phone" class="w-full md:w-1/3">電話番号</label>
                <input type="text" name="customer_phone" id="phone" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="電話番号" value="{{ old('customer_phone', request('customer_phone')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row md:items-center">
                <label for="first_login_date" class="w-full md:w-1/3">初回利用日</label>
                <div class="flex gap-x-1 items-center">
                    <input type="date" name="first_login_date_from" id="first_login_date_from" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5" value="{{ old('first_login_date_from', request('first_login_date_from')) }}">
                    <p>～</p>
                    <input type="date" name="first_login_date_to" id="first_login_date_to" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5" value="{{ old('first_login_date_to', request('first_login_date_to')) }}">
                </div>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row md:items-center">
                <label for="last_login_date" class="w-full md:w-1/3">最終利用日</label>
                <div class="flex gap-x-1 items-center">
                    <input type="date" name="last_login_date_from" id="last_login_date_from" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5" value="{{ old('last_login_date_from', request('last_login_date_from')) }}">
                    <p>～</p>
                    <input type="date" name="last_login_date_to" id="last_login_date_to" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5" value="{{ old('last_login_date_to', request('last_login_date_to')) }}">
                </div>
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">検索</button>
            </div>
        </div>
    </div>
</div>
</form>