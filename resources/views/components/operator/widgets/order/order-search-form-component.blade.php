{{-- 注文検索 --}}
<form action="{{ route('operator.order.index') }}" method="get">
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">注文検索</h2>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="order_number" class="w-full md:w-1/3">注文番号</label>
                <input type="text" name="order_number" id="order_number" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="注文番号" value="{{ old('order_number', request('order_number')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="customer_name" class="w-full md:w-1/3">顧客名</label>
                <input type="text" name="customer_name" id="customer_name" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="顧客名" value="{{ old('customer_name', request('customer_name')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="phone" class="w-full md:w-1/3">電話番号</label>
                <input type="text" name="phone" id="phone" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="電話番号" value="{{ old('phone', request('phone')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="csv_export_status" class="w-full md:w-1/3">CSV書出</label>
                <select name="csv_export_status" id="csv_export_status" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5">
                    <option value="">指定なし</option>
                    <option value="not_exported" {{ old('csv_export_status', request('csv_export_status')) === 'not_exported' ? 'selected' : '' }}>未書出</option>
                    <option value="exported" {{ old('csv_export_status', request('csv_export_status')) === 'exported' ? 'selected' : '' }}>書出済</option>
                </select>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row md:items-center">
                <label for="order_date_range" class="w-full md:w-1/3">注文日</label>
                <div class="flex-grow">
                    <input type="text" id="order_date_range" class="w-full border border-gray-300 rounded-md py-1 pl-0.5 flatpickr-range" placeholder="YYYY-MM-DD から YYYY-MM-DD">
                    <input type="hidden" name="order_date_from" id="order_date_from" value="{{ old('order_date_from', request('order_date_from')) }}">
                    <input type="hidden" name="order_date_to" id="order_date_to" value="{{ old('order_date_to', request('order_date_to')) }}">
                </div>
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">検索</button>
            </div>
        </div>
    </div>
</div>
</form>
