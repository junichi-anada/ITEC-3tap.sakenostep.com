{{-- 商品検索 --}}
<form action="{{ route('operator.item.index') }}" method="get">
<div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full">
    <div class="px-4 flex flex-col gap-y-4">
        <div>
            <h2 class="font-bold pb-2 border-b text-xl">商品検索</h2>
        </div>
        <div class="flex flex-col gap-y-4">
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="item_code" class="w-full md:w-1/3">商品コード</label>
                <input type="text" name="item_code" id="item_code" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="商品コード" value="{{ old('item_code', request('item_code')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="name" class="w-full md:w-1/3">商品名</label>
                <input type="text" name="name" id="name" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5" placeholder="商品名" value="{{ old('name', request('name')) }}">
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="category_id" class="w-full md:w-1/3">カテゴリ</label>
                <select name="category_id" id="category_id" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5">
                    <option value="">選択してください</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row md:items-center">
                <label for="published_at_range" class="w-full md:w-1/3">公開日時</label>
                <div class="flex-grow">
                    <input type="text" id="published_at_range" class="w-full border border-gray-300 rounded-md py-1 pl-0.5" placeholder="Y-m-d - Y-m-d">
                    <input type="hidden" name="published_at_from" id="published_at_from" value="{{ old('published_at_from', request('published_at_from')) }}">
                    <input type="hidden" name="published_at_to" id="published_at_to" value="{{ old('published_at_to', request('published_at_to')) }}">
                </div>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="from_source" class="w-full md:w-1/3">登録方法</label>
                <select name="from_source" id="from_source" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5">
                    <option value="">選択してください</option>
                    <option value="MANUAL" {{ old('from_source', request('from_source')) == 'MANUAL' ? 'selected' : '' }}>手動登録</option>
                    <option value="IMPORT" {{ old('from_source', request('from_source')) == 'IMPORT' ? 'selected' : '' }}>インポート</option>
                </select>
            </div>
            <div class="flex flex-col text-sm gap-y-1 md:flex-row">
                <label for="is_recommended" class="w-full md:w-1/3">おすすめ商品</label>
                <select name="is_recommended" id="is_recommended" class="flex-grow border border-gray-300 rounded-md py-1 pl-0.5">
                    <option value="">選択してください</option>
                    <option value="1" {{ old('is_recommended', request('is_recommended')) == '1' ? 'selected' : '' }}>おすすめ</option>
                    <option value="0" {{ old('is_recommended', request('is_recommended')) == '0' ? 'selected' : '' }}>通常</option>
                </select>
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">検索</button>
            </div>
        </div>
    </div>
</div>
</form>
