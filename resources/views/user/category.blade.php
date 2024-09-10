@extends('user.layouts.app')

@section('items')
{{-- favoriteItemsが空の場合 --}}
@if (empty($categories))
<div class="text-center">
    <p class="text-lg font-bold">カテゴリがありません。</p>
</div>
@elseif (count($categories) === 0)
<div class="text-center">
    <p class="text-lg font-bold">カテゴリがありません。</p>
</div>
@else
    @foreach($categories as $category)
    <div class="flex flex-col gap-y-4 border-b pb-3">
        <p class="font-bold leading-5">
            {{ $category->name }}<br>
        </p>
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
