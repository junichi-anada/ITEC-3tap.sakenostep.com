@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-y-4">
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <div class="border-t-8 border-[#F4CF41] border-l border-r border-b py-4 w-full h-[calc(100vh-5.5rem)]">
                <div class="px-4 flex flex-col gap-y-4 h-full">
                    <div class="flex flex-row items-center justify-between pb-2 border-b">
                        <h2 class="font-bold text-xl">顧客詳細情報</h2>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 h-[calc(100%-3rem)]">
                        {{-- 顧客情報コンポーネント --}}
                        <div class="w-full md:w-1/2 h-full">
                            <x-operator.widgets.customer.customer-detail-component
                                :user="$user"
                                :authenticate="$authenticate"
                                :line-user="$lineUser" />
                        </div>
                        {{-- 右側のコンポーネント群 --}}
                        <div class="w-full md:w-1/2 flex flex-col gap-y-4 h-full">
                            {{-- 注文履歴コンポーネント --}}
                            <div class="flex-grow-[2] min-h-0">
                                <x-operator.widgets.customer.order-history-component
                                    :user="$user"
                                    :orders="$orders" /> {{-- $orders をコンポーネントに渡す --}}
                            </div>
                            {{-- LINEメッセージフォームンポーネント --}}
                            @once
                                <div class="flex-grow-[1] min-h-0">
                                    <x-operator.widgets.customer.line-message-form-component
                                        :lineUser="$lineUser" />
                                </div>
                            @endonce
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/ajax/operator/customer.js') }}"></script>
    <script type="module" src="{{ asset('js/modal/operator/customer/delete.js') }}"></script>
    <script type="module" src="{{ asset('js/modal/operator/customer/restore.js') }}"></script>
@endsection
