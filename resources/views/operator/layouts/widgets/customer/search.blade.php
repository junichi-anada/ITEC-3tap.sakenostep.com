{{-- 顧客検索 --}}
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">顧客検索</h2>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_code" class="w-full md:w-1/3">お客様番号</label>
                <input type="text" name="customer_code" id="customer_code" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="お客様番号">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_name" class="w-full md:w-1/3">顧客名</label>
                <input type="text" name="customer_name" id="customer_name" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="顧客名">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="area" class="w-full md:w-1/3">住所</label>
                <input type="text" name="address" id="address" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="青森県十和田市">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="phone" class="w-full md:w-1/3">電話番号</label>
                <input type="text" name="phone" id="phone" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="電話番号">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row md:items-center">
                <label for="first_order_date" class="w-full md:w-1/3">初回注文日</label>
                <div class="flex gap-x-1 items-center">
                    <input type="date" name="first_order_date_from" id="first_order_date_from" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5">
                    <p>～</p>
                    <input type="date" name="first_order_date_to" id="first_order_date_to" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5">
                </div>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row md:items-center">
                <label for="last_order_date" class="w-full md:w-1/3">最終注文日</label>
                <div class="flex gap-x-1 items-center">
                    <input type="date" name="last_order_date_from" id="last_order_date_from" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5">
                    <p>～</p>
                    <input type="date" name="last_order_date_to" id="last_order_date_to" class="flex-shrink border border-gray-300 rounded-md py-1 pl-0.5">
                </div>
            </div>
            <div class="flex justify-center">
                <button class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">検索</button>
            </div>
        </div>
    </div>
</div>
