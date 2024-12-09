<form id="item_form" class="border py-4 h-full">
    @csrf
    <div class="flex flex-col gap-y-4 h-full w-full px-4">
        <div class="flex flex-col gap-y-8 h-full">
            <div class="flex flex-row items-center gap-x-4">
                <h3 class="text-lg font-bold border-l-8 border-[#F4CF41] pl-2">商品情報</h3>
                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-x-2 items-center">
                    <label class="">公開状態</label>
                    <div class="flex items-center">
                        @if($item->published_at)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-md">公開中</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md">非公開</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:justify-start text-sm gap-x-2 items-center">
                    <label class="">登録方法</label>
                    <div class="flex items-center">
                        @if($item->from_source === 'MANUAL')
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md">手動登録</span>
                        @else
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-md">インポート</span>
                        @endif
                    </div>
                </div>
            </div>

            <input type="hidden" id="item_id" value="{{ $item->id }}">
            <input type="hidden" name="unit_id" id="unit_id" value="0">

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="item_code" class="w-full md:w-[8vw]">商品コード</label>
                <input type="text" name="item_code" id="item_code" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $item->item_code }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="name" class="w-full md:w-[8vw]">商品名</label>
                <input type="text" name="name" id="name" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $item->name }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="category_id" class="w-full md:w-[8vw]">カテゴリ</label>
                <select name="category_id" id="category_id" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" {{ $isDeleted() ? 'disabled' : '' }}>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="description" class="w-full md:w-[8vw]">商品説明</label>
                <textarea name="description" id="description" rows="4" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" {{ $isDeleted() ? 'readonly' : '' }}>{{ $item->description }}</textarea>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="capacity" class="w-full md:w-[8vw]">容量</label>
                <input type="number" step="0.01" name="capacity" id="capacity" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $item->capacity }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="quantity_per_unit" class="w-full md:w-[8vw]">ケース入数</label>
                <input type="number" name="quantity_per_unit" id="quantity_per_unit" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $item->quantity_per_unit }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="is_recommended" class="w-full md:w-[8vw]">おすすめ商品</label>
                <div class="flex items-center">
                    <input type="checkbox" name="is_recommended" id="is_recommended" class="border border-gray-300 rounded" value="1" {{ $item->is_recommended ? 'checked' : '' }} {{ $isDeleted() ? 'disabled' : '' }}>
                    <label for="is_recommended" class="ml-2">おすすめ商品として表示する</label>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:justify-start text-sm gap-y-1">
                <label for="published_at" class="w-full md:w-[8vw]">公開日時</label>
                <input type="datetime-local" name="published_at" id="published_at" class="w-full md:max-w-[20vw] border border-gray-300 rounded-md py-1 pl-0.5" value="{{ $item->published_at ? date('Y-m-d\TH:i', strtotime($item->published_at)) : '' }}" {{ $isDeleted() ? 'readonly' : '' }}>
            </div>

            <div class="flex justify-end mt-auto gap-x-4 items-center">
                @if(!$isDeleted())
                    <div>
                        <button type="button" id="item_delete" class="bg-red-500 text-white px-8 py-1 rounded-md">削除</button>
                    </div>
                    <div>
                        <button type="button" id="item_update" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md cursor-pointer">更新</button>
                    </div>
                @endif
                <div>
                    <a href="{{ route('operator.item.index') }}" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- モーダル用の非表示要素 -->
<div id="execMode" class="hidden"></div>

<!-- 共通モーダル -->
@include('operator.layouts.widgets.modal')
