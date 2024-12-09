@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-row gap-x-4 h-full min-h-full">
        <div class="w-[30%] h-full">
            <x-operator.widgets.order.order-customer-detail-component :order="$order" />
        </div>
        <div class="w-[70%] h-full">
            <x-operator.widgets.order.order-detail-component :order="$order" />
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/ajax/operator/order.js') }}"></script>
@endsection
