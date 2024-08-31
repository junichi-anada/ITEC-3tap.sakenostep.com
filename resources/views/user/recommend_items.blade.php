@include('user.layouts.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- content -->
<div class="flex flex-col pb-2 px-2 h-[calc(100vh-(3rem+6rem+2px))] bg-[#F6F6F6]">

    <!-- Items -->
    <div class="overflow-y-auto bg-white py-3 px-3 h-full">
        <div class="flex flex-col gap-y-3">
            <?php /* recommendedItemsが空の場合 */ ?>
            @if (empty($recommendedItems))
            <div class="text-center">
                <p class="text-lg font-bold">おすすめ商品がありません。</p>
            </div>
            <?php /* recommendedItemsが0件の場合 */ ?>
            @elseif (count($recommendedItems) === 0)
            <div class="text-center">
                <p class="text-lg font-bold">おすすめ商品がありません。</p>
            </div>
            <?php /* recommendedItemsがあった場合 */ ?>
            @else
                @foreach($recommendedItems as $recommendedItem)
                <div class="flex flex-col gap-y-4 border-b pb-3">
                    <p class="font-bold leading-5">
                        {{ $recommendedItem->name }}<br>
                        <span class="text-xs font-normal">{{ $recommendedItem->maker_name }}</span>
                    </p>
                    <div class="flex gap-x-4">
                        <div>
                            <button class="border-2 border-[#00D41C] px-2 py-1">
                                <div class="flex items-center gap-x-2 py-1">
                                    <span class="material-symbols-outlined text-[#00D41C]">add_circle</span>
                                    <span class="text-xs">注文リスト</span>
                                </div>
                            </button>
                        </div>
                        <div>
                            <!-- 初期表示で登録状況に応じてボタンを切り替え -->
                            <button class="border-2 border-[#008CD4] px-2 py-1 add-to-favorites
                                    {{ in_array($recommendedItem->id, $favoriteItems) ? 'hidden' : '' }}"
                                    data-item-id="{{ $recommendedItem->id }}"
                                    data-site-id="{{ $recommendedItem->site_id }}">
                                <div class="flex items-center gap-x-2 py-1">
                                    <span class="material-symbols-outlined text-[#008CD4]">add_circle</span>
                                    <span class="text-xs">マイリスト</span>
                                </div>
                            </button>
                            <button class="border-2 border-[#008CD4] bg-[#008CD4] px-2 py-1 del-to-favorites
                                    {{ in_array($recommendedItem->id, $favoriteItems) ? '' : 'hidden' }}"
                                    data-item-id="{{ $recommendedItem->id }}"
                                    data-site-id="{{ $recommendedItem->site_id }}">
                                <div class="flex items-center gap-x-2 py-1">
                                    <span class="material-symbols-outlined text-white">check_circle</span>
                                    <span class="text-xs text-white">マイリスト</span>
                                </div>
                            </button>
                        </div>
                        {{-- <div class="flex items-center">
                            <button class="border px-1.5 py-0.5 border-r-0 text-sm">－</button>
                            <input type="text" name="" value="1"
                                class="w-16 border border-r-0 text-center py-0.5 text-sm">
                            <button class="border px-1.5 py-0.5 text-sm">＋</button>
                            <span class="inline-block ml-2 text-sm">本</span>
                        </div> --}}
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
            <a href="{{ route('order') }}" class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
                注文リストへ
            </a>
        </div>
    </div>
</div>
<!-- //content -->

<script src="{{ asset('js/favorite/add.js') }}"></script>
<script src="{{ asset('js/favorite/delete.js') }}"></script>

@include('user.layouts.footer')
