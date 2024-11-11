@extends('customer.layouts.app')

@section('items')
{{-- ordersが空の場合 --}}
@if (empty($orders))
<div class="text-center">
    <p class="text-lg font-bold">注文履歴がありません。</p>
</div>
{{-- ordersが0件の場合 --}}
@elseif (count($orders) === 0)
<div class="text-center">
    <p class="text-lg font-bold">注文履歴がありません。</p>
</div>
{{-- orderItemsがあった場合 --}}
@else
    @foreach($orders as $order)
    <!-- Item -->
    <div class="flex flex-row gap-y-4 items-center justify-between border-b pb-3">
        <div class="flex flex-col gap-y-2 flex-1">
            <p class="font-bold">注文日時: {{ $order->ordered_at }}</p>
            <p class="font-bold text-sm">注文番号:<br>{{ $order->order_code }}</p>
        </div>
        <div class="flex-glow">
            {{-- 注文詳細ボタン --}}
            <a href="{{ route('user.history.detail', ['order_code' => $order->order_code]) }}"
                class="inline-block border-2 border-[#F4CF41] bg-[#F4CF41] rounded-md px-3 py-1.5">
                <div class="flex items-center gap-x-1 py-0.5">
                    <span class="text-xs">詳細</span>
                </div>
            </a>
        </div>
    </div>
    <!-- //Item -->
    @endforeach
@endif
@endsection

@section('control')
<a href="{{ route('user.order.item.list') }}" class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
    <div class="flex items-center gap-x-1">
        <span class="material-symbols-outlined text-xl text-white">shopping_cart</span>
        <span>注文リストへ</span>
    </div>
</a>
@endsection

@section('js')
@endsection
