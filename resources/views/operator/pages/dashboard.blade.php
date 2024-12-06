@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start h-[calc(100vh-7.5rem)] xl:flex-row gap-y-4 xl:text-[1vw]">
        <div class="flex flex-col xl:w-[70%] gap-y-8">
            <div class="flex flex-row gap-x-8">
                <x-widget.operator.order.monthly-count-component />
                <x-widget.operator.order.today-count-component />
            </div>
            <div class="flex flex-row gap-x-8">
                <div class="flex flex-col gap-y-8 xl:w-1/2">
                    <x-widget.operator.customer.regist-each-area-component />
                    <x-widget.operator.item.popular-ranking-component />
                </div>
                <div class="flex flex-col gap-y-8 xl:w-1/2">
                    <x-widget.operator.order.each-area-order-component />
                </div>
            </div>
        </div>
        <div class="">
            @include('operator.layouts.widgets.system_info')
        </div>
    </div>
@endsection
