@extends('operator.layouts.app')

@section('content')
    <div class="flex gap-x-8 justify-start h-[calc(100vh-7.5rem)]">
        <div class="flex flex-col gap-y-4 w-1/4">
            @include('operator.layouts.widgets.user')
            @include('operator.layouts.widgets.popular_item')
        </div>
        <div class="flex flex-col gap-y-4 w-1/3">
            @include('operator.layouts.widgets.area_sales')
        </div>
        <div class="">
            @include('operator.layouts.widgets.system_info')
        </div>
    </div>
@endsection

