@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start xl:h-[calc(100vh-7.5rem)] xl:flex-row gap-y-4 xl:text-[1vw]">
        <div class="flex flex-col xl:w-[50%] gap-y-8">
            <div class="flex flex-col gap-y-4 w-full xl:flex-row gap-x-8">
                <x-operator.widgets.order.monthly-count-component />
                <x-operator.widgets.order.today-count-component />
            </div>
            <div class="flex flex-col w-full gap-y-4 xl:flex-row gap-x-8">
                <div class="flex flex-col gap-y-8 w-full xl:w-1/2">
                    <x-operator.widgets.customer.customer-registration-component />
                    <x-operator.widgets.item.popular-ranking-component />
                </div>
                <div class="flex flex-col gap-y-8 w-full xl:w-1/2">
                    <x-operator.widgets.order.each-area-order-component />
                </div>
            </div>
        </div>
        <div class="w-full xl:w-[50%]">
            <x-operator.widgets.system-info.system-info-component />
        </div>
    </div>
@endsection
