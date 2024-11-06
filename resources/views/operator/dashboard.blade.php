@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start h-[calc(100vh-7.5rem)] md:flex-row">
        <div class="flex flex-col gap-y-4 md:w-1/4">
            <x-operator.customer-widget />
            <x-operator.popular-item-widget />
        </div>
        <div class="flex flex-col gap-y-4 md:w-1/3">
            <x-operator.area-order-widget />
        </div>
        <div class="">
            @include('operator.layouts.widgets.system_info')
        </div>
    </div>
@endsection

