@include('user.layouts.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- content -->
<div class="flex flex-col pb-2 px-2 h-[calc(100vh-(4rem+6rem+2px+60px))] bg-[#F6F6F6]">

    <!-- Items -->
    <div class="overflow-y-auto bg-white py-3 px-3 h-full">
        <div class="flex flex-col gap-y-3">
            <?php /* orderItemsが空の場合 */ ?>
            @if (empty($orderItems))
            <div class="text-center">
                <p class="text-lg font-bold">注文リストに商品がありません。</p>
            </div>
            <?php /* orderItemsが0件の場合 */ ?>
            @elseif (count($orderItems) === 0)
            <div class="text-center">
                <p class="text-lg font-bold">注文リストに商品がありません。</p>
            </div>
            <?php /* orderItemsがあった場合 */ ?>
            @else
                @foreach($orderItems as $orderItem)
                <div class="flex flex-col gap-y-4 border-b pb-3">
                    <p class="font-bold leading-5">
                        {{ $orderItem->item->name }}<br>
                        <span class="text-xs font-normal">{{ $orderItem->item->maker_name }}</span>
                    </p>
                    <div class="flex justify-between">
                        <div class="flex gap-x-4">
                            <div>
                                <button class="border-2 border-red-500 px-1.5 del-to-order"
                                        data-detail-code="{{ $orderItem->detail_code }}">
                                    <div class="flex items-center gap-x-1 py-0.5">
                                        <span class="material-symbols-outlined text-red-500 text-3xl">remove_shopping_cart</span>
                                        <span class="text-xs">削除</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <button class="border px-1.5 py-0.5 border-r-0 text-lg">－</button>
                            <input type="text" name="volume" value="{{ $orderItem->volume }}"
                                class="w-10 border border-r-0 text-center py-0.5 text-lg">
                            <button class="border px-1.5 py-0.5 text-lg">＋</button>
                            <span class="inline-block ml-2 text-lg">本</span>
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
            <button class="bg-red-600 text-white px-7 py-1.5 rounded-xl disabled:bg-gray-300 disabled:cursor-not-allowed" id="del-all-order" {{ empty($orderItems) || count($orderItems) === 0 ? 'disabled': ''}}>
                全て削除
            </button>
            <button class="bg-red-600 text-white px-6 py-1.5 rounded-xl disabled:bg-gray-300 disabled:cursor-not-allowed" id="openOrderModal" {{ empty($orderItems) || count($orderItems) === 0 ? 'disabled': ''}}>
                注文する
            </button>
        </div>
    </div>
</div>
<!-- //content -->

<script src="{{ asset('js/modal/open_order.js') }}"></script>
<script src="{{ asset('js/volume.js') }}"></script>
<script src="{{ asset('js/ajax/order.js') }}"></script>

@include('user.layouts.footer')
