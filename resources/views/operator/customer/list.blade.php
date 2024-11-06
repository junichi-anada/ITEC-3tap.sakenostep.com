@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start h-[calc(100vh-7.5rem)] md:flex-row">
        <div class="flex flex-col gap-y-2 w-full md:w-auto">
            @include('operator.layouts.widgets.customer.search')
            @include('operator.layouts.widgets.customer.tool')
        </div>
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <x-operator.customer-list-widget :customers="$customers" />
        </div>
    </div>
@endsection
