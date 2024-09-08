@include('user.layouts.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- content -->
<div class="flex flex-col pb-2 px-2 h-[calc(100vh-(4rem+6rem+2px+60px))] bg-[#F6F6F6]">

    <!-- Items -->
    <div class="overflow-y-auto bg-white py-3 px-3 h-full">
        <div class="flex flex-col gap-y-3">
            <?php /* favoriteItemsが空の場合 */ ?>
            @if (empty($favoriteItems))
            <div class="text-center">
                <p class="text-lg font-bold">お気に入りに商品がありません。</p>
            </div>
            @elseif (count($favoriteItems) === 0)
            <div class="text-center">
                <p class="text-lg font-bold">お気に入りに商品がありません。</p>
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
                                <button class="border-2 border-[#00D41C] px-1.5 add-to-order
                                        {{ $unOrderItem ? 'hidden' : '' }}"
                                        data-item-id="{{ $favoriteItem->item_id }}"
                                        data-site-id="{{ $favoriteItem->site_id }}">
                                    <div class="flex items-center gap-x-1 py-0.5">
                                        <span class="material-symbols-outlined text-[#00D41C] text-3xl">add_shopping_cart</span>
                                        <span class="text-xs">注文</span>
                                    </div>
                                </button>
                                <!-- 注文リストから削除ボタン -->
                                <button class="border-2 border-[#00D41C] bg-[#00D41C] px-1.5 del-to-order
                                        {{ $unOrderItem ? '' : 'hidden' }}"
                                        data-detail-code="{{ $unOrderItem['detail_code'] ?? '' }}">
                                    <div class="flex items-center gap-x-1 py-0.5">
                                        <span class="material-symbols-outlined text-white text-3xl">remove_shopping_cart</span>
                                        <span class="text-xs text-white">削除</span>
                                    </div>
                                </button>
                            </div>
                            <div>
                                <button class="border-2 border-red-500 px-1.5 del-to-favorites"
                                        data-item-id="{{ $favoriteItem->item_id }}"
                                        data-site-id="{{ $favoriteItem->site_id }}">
                                    <div class="flex items-center gap-x-1 py-0.5">
                                        <span class="material-symbols-outlined text-red-500 text-3xl">heart_minus</span>
                                        <span class="text-xs">削除</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center ml-auto">
                            <button class="border px-1.5 py-0.5 border-r-0 text-lg">－</button>
                            <input type="text" name="volume" value="{{ $unOrderItem['volume'] ?? 1 }}"
                                class="w-10 border border-r-0 text-center py-0.5 text-lg">
                            <button class="border px-1.5 py-0.5 text-lg">＋</button>
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
    <div class="bg-transparent pt-4 pb-2 h-[60px] relative bottom-0">
        <div class="flex justify-center gap-x-16">
            <a href="{{ route('user.order.item.list') }}" class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
                <div class="flex items-center gap-x-1">
                    <span class="material-symbols-outlined text-xl text-white">shopping_cart</span>
                    <span>注文リストへ</span>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- //content -->

<script src="{{ asset('js/volume.js') }}"></script>
<script src="{{ asset('js/ajax/favorite.js') }}"></script>
<script src="{{ asset('js/ajax/order.js') }}"></script>

@include('user.layouts.footer')
