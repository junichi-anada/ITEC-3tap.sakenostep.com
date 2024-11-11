{{-- 顧客検索 --}}
<form action="{{ route('operator.customer.search') }}" method="post">
@csrf
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">商品検索</h2>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_code" class="w-full md:w-1/3">商品番号</label>
                <input type="text" name="customer_code" id="customer_code" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="お客様番号" value="{{ old('customer_code', request('customer_code')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_name" class="w-full md:w-1/3">カテゴリ</label>
                <select name="category" id="category">
                    <option value="ブランデー"></option>
                    <option value="ウイスキー"></option>
                    <option value="焼酎"></option>
                    <option value="リキュール"></option>
                </select>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_address" class="w-full md:w-1/3">メーカー</label>
                <input type="text" name="maker" id="maker" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="青森県十和田市" value="{{ old('customer_address', request('customer_address')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_phone" class="w-full md:w-1/3">商品名</label>
                <input type="text" name="customer_phone" id="phone" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="電話番号" value="{{ old('customer_phone', request('customer_phone')) }}">
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">検索</button>
            </div>
        </div>
    </div>
</div>
</form>