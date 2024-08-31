@include('user.layouts.header')

<!-- content -->
<div class="flex flex-col pb-2 px-2 h-[calc(100vh-(3rem+6rem+2px))] bg-[#F6F6F6]">

    <!-- Items -->
    <div class="overflow-y-auto bg-white py-3 px-3 h-full">
        <div class="flex flex-col gap-y-3">
            <?php /* favoriteItemsが空の場合 */ ?>
            @if (empty($favoriteItems))
            <div class="text-center">
                <p class="text-lg font-bold">マイリストに商品がありません。</p>
            </div>
            @elseif (count($favoriteItems) === 0)
            <div class="text-center">
                <p class="text-lg font-bold">マイリストに商品がありません。</p>
            </div>
            @else
                @foreach($favoriteItems as $favoriteItem)
                <div class="flex flex-col gap-y-4 border-b pb-3">
                    <p class="font-bold leading-5">
                        {{ $favoriteItem->item->name }}<br>
                        <span class="text-xs font-normal">{{ $favoriteItem->item->maker_name }}</span>
                    </p>
                    <div class="flex justify-between">
                        <div>
                            <button class="border-2 border-red-500 px-2 py-1">
                                <div class="flex items-center gap-x-2 py-1">
                                    <span class="material-symbols-outlined text-red-500">cancel</span>
                                    <span class="text-xs">リストから削除</span>
                                </div>
                            </button>
                        </div>
                        <div class="flex items-center">
                            <button class="border px-1.5 py-0.5 border-r-0 text-lg">－</button>
                            <input type="text" name="" value="1"
                                class="w-16 border border-r-0 text-center py-0.5 text-lg">
                            <button class="border px-1.5 py-0.5 text-lg">＋</button>
                            <span class="inline-block ml-2 text-lg">本</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
            <!-- //Item -->
        </div>
    </div>
    <!-- //Items -->

    <!-- Control -->
    <div class="bg-transparent pt-6 pb-4 h-[120px] relative bottom-0">
        <div class="flex justify-center gap-x-16">
            <button class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
                全て削除
            </button>
            <button class="bg-red-600 text-white px-6 py-1.5  rounded-xl" id="openOrderModal">
                注文する
            </button>
        </div>
    </div>
</div>
<!-- //content -->

<script src="{{ asset('js/favorite/add.js') }}"></script>
<script src="{{ asset('js/favorite/delete.js') }}"></script>

@include('user.layouts.footer')
