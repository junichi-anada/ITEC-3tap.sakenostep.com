@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-y-4">
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
                <div class="px-4 flex flex-col gap-y-4 h-full">
                    <div class="flex flex-row items-center justify-between pb-2 border-b">
                        <h2 class="font-bold text-xl">商品詳細情報</h2>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 h-[calc(100%-3rem)]">
                        {{-- 商品情報コンポーネント --}}
                        <div class="w-full md:w-1/2 h-full">
                            <x-operator.widgets.item.item-detail-component
                                :item="$item" />
                        </div>
                        {{-- 右側のコンポーネント群 --}}
                        <div class="w-full md:w-1/2 flex flex-col gap-y-4 h-full">
                            {{-- 将来的な拡張用のスペース --}}
                            {{-- 例：注文実績、在庫履歴、価格履歴など --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/ajax/operator/item.js') }}"></script>
    <script type="module" src="{{ asset('js/modal/operator/item/delete.js') }}"></script>
@endsection
