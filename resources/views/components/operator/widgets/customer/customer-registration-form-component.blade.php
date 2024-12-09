<form id="customer_form">
    @csrf
    <div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
        <div class="px-4 flex flex-col gap-y-4 h-full w-full">
            <div class="flex flex-row items-center justify-between pb-2 border-b">
                <h2 class="font-bold  text-xl">顧客登録</h2>
            </div>
                <div class="flex flex-col gap-y-8 h-full" id="customer_regist_data">
                    <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">顧客情報</h3>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                        <label for="name" class="w-full md:w-[5vw]">顧客名</label>
                        <input type="text" name="name" id="name" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" placeholder="顧客名">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                        <label for="phone" class="w-full md:w-[5vw]">電話番号</label>
                        <input type="text" name="phone" id="phone" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" placeholder="電話番号">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                        <label for="phone2" class="w-full md:w-[5vw]">電話番号2</label>
                        <input type="text" name="phone2" id="phone2" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" placeholder="電話番号2（任意）">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                        <label for="fax" class="w-full md:w-[5vw]">FAX番号</label>
                        <input type="text" name="fax" id="fax" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" placeholder="FAX番号（任意）">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                        <label for="postal_code" class="w-full md:w-[5vw]">郵便番号</label>
                        <input type="text" name="postal_code" id="postal_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" placeholder="郵便番号">
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                        <label for="address" class="w-full md:w-[5vw]">住所</label>
                        <input type="text" name="address" id="address" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" placeholder="青森県十和田市">
                    </div>
                    <div class="flex justify-end mt-auto gap-x-4 items-center">
                        <div>
                            <button type="button" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer" id="customer_regist">登録</button>
                        </div>
                        <div>
                            <a href="{{ route('operator.customer.index') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">一覧に戻る</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- モーダル用の非表示要素 -->
<div id="execMode" class="hidden"></div>

<!-- 共通モーダル -->
@include('operator.layouts.widgets.modal')
