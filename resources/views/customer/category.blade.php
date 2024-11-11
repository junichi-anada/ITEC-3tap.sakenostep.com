@extends('customer.layouts.app')

@section('items')

<ul class="flex text-sm text-gray-600">
    <li>商品一覧</li>
</ul>

{{-- categoriesが空の場合 --}}
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
        <a href="{{ route('user.category.item.list', ['code' => $category->category_code]) }}" class="flex items-center px-2">
            <p class="font-bold">{{ $category->name }}</p>
            <span class="material-symbols-outlined text-2xl text-black ml-auto">chevron_right</span>
        </a>
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
