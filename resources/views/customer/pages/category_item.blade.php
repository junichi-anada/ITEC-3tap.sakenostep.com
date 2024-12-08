@extends('customer.layouts.app')

@section('items')

<ul class="flex text-sm text-gray-600">
    <li class="after:content-['>'] after:mx-1"><a href="{{ route('user.category.list') }}">商品一覧</a></li>
    <li>{{ $category->name }}</li>
</ul>

{{-- itemsが空の場合 --}}
@if (empty($items))
<div class="text-center">
    <p class="text-lg font-bold">商品がありません。</p>
</div>
{{-- itemsが0件の場合 --}}
@elseif (count($items) === 0)
<div class="text-center">
    <p class="text-lg font-bold">商品がありません。</p>
</div>
{{-- itemsがあった場合 --}}
@else
    @csrf
    @foreach($items as $item)
    <!-- Item -->
    <div class="flex flex-col gap-y-4 border-b pb-3">
        <p class="font-bold leading-5">
            {{ $item['name'] }}<br>
            <span class="text-xs font-normal">{{ $item['maker_name'] }}</span>
        </p>
        <div class="flex gap-x-4">
            <div>
                {{-- 注文リストへの追加ボタン --}}
                <button class="border-2 border-[#00D41C] px-1.5 add-to-order
                        {{ $item['score1'] ? 'hidden' : '' }}"
                        data-item-code="{{ $item['item_code'] }}">
                    <div class="flex items-center gap-x-1 py-0.5">
                        <span class="material-symbols-outlined text-[#00D41C] text-3xl">add_shopping_cart</span>
                        <span class="text-xs">注文</span>
                    </div>
                </button>
                {{-- 注文リストから削除ボタン --}}
                <button class="border-2 border-[#00D41C] bg-[#00D41C] px-1.5 del-to-order
                        {{ $item['score1'] ? '' : 'hidden' }}"
                        data-item-code="{{ $item['item_code'] ?? '' }}">
                    <div class="flex items-center gap-x-1 py-0.5">
                        <span class="material-symbols-outlined text-white text-3xl">remove_shopping_cart</span>
                        <span class="text-xs text-white">削除</span>
                    </div>
                </button>
            </div>
            <div>
                {{-- 初期表示で登録状況に応じてボタンを切り替え --}}
                <button class="border-2 border-[#008CD4] px-1.5 add-to-favorites
                        {{ $item['score2'] ? 'hidden' : '' }}"
                        data-item-code="{{ $item['item_code'] }}">
                    <div class="flex items-center gap-x-1 py-0.5">
                        <span class="material-symbols-outlined text-[#008CD4] text-3xl">heart_plus</span>
                        <span class="text-xs">追加</span>
                    </div>
                </button>
                <button class="border-2 border-[#008CD4] bg-[#008CD4] px-1.5 del-to-favorites
                        {{ $item['score2'] ? '' : 'hidden' }}"
                        data-item-code="{{ $item['item_code'] }}"">
                    <div class="flex items-center gap-x-1 py-0.5">
                        <span class="material-symbols-outlined text-white text-3xl">heart_minus</span>
                        <span class="text-xs text-white">削除</span>
                    </div>
                </button>
            </div>
            <div class="flex items-center ml-auto">
                <div class="border px-1.5 py-0.5 border-r-0 text-lg volume-minus">－</div>
                <input type="text" name="volume" value="{{ $item['unorderedVolume'] }}"
                    class="w-10 border border-r-0 text-center py-0.5 text-lg volume-input">
                <div class="border px-1.5 py-0.5 text-lg volume-plus">＋</div>
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
 * 商品リストのコントロール
 *     ・注文リストへの遷移ボタン
 */
--}}
@section('control')
<a href="{{ route('user.order.item.list') }}" class="bg-red-600 text-white px-4 py-1.5 rounded-xl">
    <div class="flex items-center gap-x-1">
        <span class="material-symbols-outlined text-xl text-white">shopping_cart</span>
        <span>注文リストへ</span>
    </div>
</a>
<a href="{{ route('user.category.list') }}" class="bg-red-600 text-white px-4 py-1.5 rounded-xl">
    <div class="flex items-center gap-x-1">
        <span class="material-symbols-outlined text-xl">format_list_bulleted</span>
        <span>商品一覧へ</span>
    </div>
</a>
@endsection

@section('js')
<script src="{{ asset('js/volume.js') }}"></script>
<script src="{{ asset('js/ajax/favorite.js') }}"></script>
<script type="module" src="{{ asset('js/ajax/order.js') }}"></script>
@endsection