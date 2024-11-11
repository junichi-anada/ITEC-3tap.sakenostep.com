@extends('customer.layouts.app')

@section('items')
{{-- orderDetailsが空の場合 --}}
@if (empty($orderDetails))
<div class="text-center">
    <p class="text-lg font-bold">注文リストに商品がありません。</p>
</div>
{{-- orderDetailsが0件の場合 --}}
@elseif (count($orderDetails) === 0)
<div class="text-center">
    <p class="text-lg font-bold">注文リストに商品がありません。</p>
</div>
{{-- orderDetailsがあった場合 --}}
@else
    <form action="{{ route('user.order.item.list.add.all') }}" method="post" id="add-to-all-order-form">
        @csrf
        @foreach($orderDetails as $orderDetail)
        <!-- 一回目だけ注文番号を表示 -->
        @if ($loop->first)
        <p class="text-sm">注文番号：{{ $orderDetail->order->order_code }}</p>
        <input type="hidden" name="order_code" value="{{ $orderDetail->order->order_code }}">
        @endif
        <!-- Item -->
        <div class="flex flex-col gap-y-4 border-b pb-3 pt-3">
            <p class="font-bold leading-5">
                {{ $orderDetail->item->name }}<br>
                <span class="text-xs font-normal">{{ $orderDetail->item->maker_name }}</span>
                <input type="hidden" name="item_id[]" value="{{ $orderDetail->item_id }}" data-item-id="{{ $orderDetail->item_id }}">
            </p>
            <div class="flex justify-between">
                <div class="flex gap-x-4">
                </div>
                <div class="flex items-center">
                    <div class="border px-1.5 py-0.5 border-r-0 text-lg volume-minus">－</div>
                    <input type="text" name="volume[]" value="{{ $orderDetail->volume }}"
                        class="w-10 border border-r-0 text-center py-0.5 text-lg volume-input" data-item-id="{{ $orderDetail->item_id }}">
                    <div class="border px-1.5 py-0.5 text-lg volume-plus">＋</div>
                    <span class="inline-block ml-2 text-lg">本</span>
                </div>
            </div>
        </div>
        <!-- //Item -->
        @endforeach
    </form>
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
{{-- クリックしたら表示されている全アイテムを注文リストに反映させる --}}
<button class="bg-red-600 text-white px-6 py-1.5 rounded-xl disabled:bg-gray-300 disabled:cursor-not-allowed" id="add-to-all-order" {{ empty($orderDetails) || count($orderDetails) === 0 ? 'disabled': ''}} data-order-code="{{ $orderDetail->order->order_code }}">
    注文リストに反映
</button>
@endsection

@section('js')
<script src="{{ asset('js/volume.js') }}"></script>
<script type="module" src="{{ asset('js/ajax/order.js') }}"></script>
@endsection
