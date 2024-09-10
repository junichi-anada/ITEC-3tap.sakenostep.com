@extends('user.layouts.app')

@section('items')
{{-- favoriteItemsが空の場合 --}}
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
    <!-- //Item -->
    @endforeach
@endif
@endsection

{{--
/**
 * お気に入りリストのコントロール
    ・注文リストへの遷移ボタン
 */
--}}
@section('control')
<a href="{{ route('user.order.item.list') }}" class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
    <div class="flex items-center gap-x-1">
        <span class="material-symbols-outlined text-xl text-white">shopping_cart</span>
        <span>注文リストへ</span>
    </div>
</a>
@endsection

@section('js')
<script src="{{ asset('js/volume.js') }}"></script>
<script src="{{ asset('js/ajax/favorite.js') }}"></script>
<script src="{{ asset('js/ajax/order.js') }}"></script>
@endsection
