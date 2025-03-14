@extends('customer.layouts.app')

@section('items')
{{-- メッセージがある場合に表示 --}}
@if ($message)
<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 py-1 bg-[#f0f9ff] px-1" role="alert">
    <p>{{ $message }}</p>
</div>
@endif

{{-- orderItemsが空の場合 --}}
@if (empty($orderItems))
<!-- <div class="text-center">
    <p class="text-lg font-bold">注文リストに商品がありません。</p>
</div> -->
{{-- orderItemsが0件の場合 --}}
@elseif (count($orderItems) === 0)
<!-- <div class="text-center">
    <p class="text-lg font-bold">注文リストに商品がありません。</p>
</div> -->
{{-- orderItemsがあった場合 --}}
@else
    @csrf
    @foreach($orderItems as $orderItem)
    <!-- Item -->
    <div class="flex flex-col gap-y-4 border-b pb-3">
        <p class="font-bold leading-5">
            {{ $orderItem->item->name }}<br>
            <span class="text-xs font-normal">{{ $orderItem->item->maker_name }}</span>
        </p>
        <div class="flex justify-between">
            <div class="flex gap-x-4">
                <div>
                    {{-- 注文リストから削除ボタン --}}
                    <button class="border-2 border-red-500 px-1.5 del-to-order"
                            data-item-code="{{ $orderItem->item->item_code }}">
                        <div class="flex items-center gap-x-1 py-0.5">
                            <span class="material-symbols-outlined text-red-500 text-3xl">remove_shopping_cart</span>
                            <span class="text-xs">削除</span>
                        </div>
                    </button>
                </div>
            </div>
            <div class="flex items-center">
                <div class="border px-1.5 py-0.5 border-r-0 text-lg volume-minus">－</div>
                <input type="text" name="volume" value="{{ $orderItem->volume }}"
                    class="w-10 border border-r-0 text-center py-0.5 text-lg volume-input">
                <div class="border px-1.5 py-0.5 text-lg volume-plus">＋</div>
                <span class="inline-block ml-2 text-lg">本</span>
            </div>
        </div>
    </div>
    <!-- //Item -->
    @endforeach
@endif
@endsection

{{--
/**
 * 注文リストのコントロール
    ・全て削除ボタン
    ・注文するボタン
 */
--}}
@section('control')

{{-- クリックしたら/remove/allにリクエスト --}}
<button type="button" class="bg-red-600 text-white px-6 py-1.5 rounded-xl disabled:bg-gray-300 disabled:cursor-not-allowed" id="del-all-order" {{ empty($orderItems) || count($orderItems) === 0 ? 'disabled': ''}}>
    全て削除
</button>

{{-- クリックしたら/orderにリクエスト --}}
<button type="button" class="bg-red-600 text-white px-6 py-1.5 rounded-xl disabled:bg-gray-300 disabled:cursor-not-allowed" id="openOrderModal" {{ empty($orderItems) || count($orderItems) === 0 ? 'disabled': ''}}>
    注文する
</button>
@endsection

@section('js')
<script src="{{ asset('js/volume.js') }}"></script>
<script type="module" src="{{ asset('js/ajax/order.js') }}"></script>
@endsection
