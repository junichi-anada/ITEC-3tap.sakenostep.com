@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start h-[calc(100vh-7.5rem)] xl:flex-row gap-y-4 xl:text-[1vw]">
        <div class="flex flex-col xl:w-[70%] gap-y-8">
            <div class="flex flex-row gap-x-8">
                <x-operator.widgets.order.monthly-count-component />
                <x-operator.widgets.order.today-count-component />
            </div>
            <div class="flex flex-row gap-x-8">
                <div class="flex flex-col gap-y-8 xl:w-1/2">
                    <x-operator.widgets.customer.customer-registration-component />
                    <x-operator.widgets.item.popular-ranking-component />
                </div>
                <div class="flex flex-col gap-y-8 xl:w-1/2">
                    <x-operator.widgets.order.each-area-order-component />
                </div>
            </div>
        </div>
        <div class="">
            <x-operator.widgets.system-info.system-info-component />
        </div>
    </div>
@endsection
