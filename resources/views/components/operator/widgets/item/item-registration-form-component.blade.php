{{-- 商品登録フォーム --}}
<form id="item_form">
    @csrf
    <div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-7.5rem)]">
        <div class="px-4 flex flex-col gap-y-4 h-full w-full">
            <div class="flex flex-row items-center justify-between pb-2 border-b">
                <h2 class="font-bold text-xl">商品登録</h2>
            </div>
            <div class="flex flex-col gap-y-8 h-full" id="item_regist_data">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">商品情報</h3>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="item_code" class="w-full md:w-[5vw]">商品コード</label>
                    <input type="text" name="item_code" id="item_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" required>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="name" class="w-full md:w-[5vw]">商品名</label>
                    <input type="text" name="name" id="name" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" required>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="category_id" class="w-full md:w-[5vw]">カテゴリ</label>
                    <select name="category_id" id="category_id" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" required>
                        <option value="">選択してください</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="description" class="w-full md:w-[5vw]">商品説明</label>
                    <textarea name="description" id="description" rows="4" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5"></textarea>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="capacity" class="w-full md:w-[5vw]">容量</label>
                    <input type="number" step="0.01" name="capacity" id="capacity" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5">
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="quantity_per_unit" class="w-full md:w-[5vw]">ケース入数</label>
                    <input type="number" name="quantity_per_unit" id="quantity_per_unit" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5">
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="is_recommended" class="w-full md:w-[5vw]">おすすめ商品</label>
                    <div class="flex items-center w-full md:max-w-[20vw]">
                        <input type="checkbox" name="is_recommended" id="is_recommended" class="border border-gray-300 rounded" value="1">
                        <label for="is_recommended" class="ml-2">おすすめ商品として表示する</label>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                    <label for="published_at" class="w-full md:w-[5vw]">公開日時</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5">
                </div>

                <input type="hidden" name="from_source" value="MANUAL">

                <div class="flex justify-end mt-auto gap-x-4 items-center">
                    <div>
                        <button type="button" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer" id="item_regist">登録</button>
                    </div>
                    <div>
                        <a href="{{ route('operator.item.index') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">一覧に戻る</a>
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
