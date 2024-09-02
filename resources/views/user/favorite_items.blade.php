@include('user.layouts.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                @php
                    // $unorderedItems の中で、現在の $favoriteItem の item_id に一致する detail_code を探す
                    $unOrderItem = collect($unorderedItems)->firstWhere('item_id', $favoriteItem->item_id);
                @endphp
                <div class="flex flex-col gap-y-4 border-b pb-3">
                    <p class="font-bold leading-5">
                        {{ $favoriteItem->item->name }}<br>
                        <span class="text-xs font-normal">{{ $favoriteItem->item->maker_name }}</span>
                    </p>
                    <div class="flex justify-between">
                        <div class="flex gap-x-4">
                            <div>
                                <!-- 注文リストへの追加ボタン -->
                                <button class="border-2 border-[#00D41C] px-2 py-1 add-to-order
                                        {{ $unOrderItem ? 'hidden' : '' }}"
                                        data-item-id="{{ $favoriteItem->item_id }}"
                                        data-site-id="{{ $favoriteItem->site_id }}">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <span class="material-symbols-outlined text-[#00D41C]">add_circle</span>
                                        <span class="text-xs">注文リスト</span>
                                    </div>
                                </button>
                                <!-- 注文リストから削除ボタン -->
                                <button class="border-2 border-[#00D41C] bg-[#00D41C] px-2 py-1 del-to-order
                                        {{ $unOrderItem ? '' : 'hidden' }}"
                                        data-detail-code="{{ $unOrderItem['detail_code'] ?? '' }}">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <span class="material-symbols-outlined text-white">check_circle</span>
                                        <span class="text-xs text-white">注文リスト</span>
                                    </div>
                                </button>
                            </div>
                            <div>
                                <button class="border-2 border-red-500 px-2 py-1 del-to-favorites"
                                        data-item-id="{{ $favoriteItem->item_id }}"
                                        data-site-id="{{ $favoriteItem->site_id }}">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <span class="material-symbols-outlined text-red-500">delete_forever</span>
                                        <span class="text-xs">リスト削除</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center ml-auto">
                            <button class="border px-1.5 py-0.5 border-r-0">－</button>
                            <input type="text" name="" value="1"
                                class="w-16 border border-r-0 text-center py-0.5">
                            <button class="border px-1.5 py-0.5">＋</button>
                            <span class="inline-block ml-2 text-sm">本</span>
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
            <a href="{{ route('user.order') }}" class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
                注文リストへ
            </a>
        </div>
    </div>
</div>
<!-- //content -->

<script src="{{ asset('js/volume.js') }}"></script>
<script src="{{ asset('js/ajax/favorite.js') }}"></script>
<script src="{{ asset('js/ajax/order.js') }}"></script>

@include('user.layouts.footer')
