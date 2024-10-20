@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start h-[calc(100vh-7.5rem)] md:flex-row">
        <div class="flex flex-col gap-y-4 md:flex-grow">
            @include('operator.layouts.widgets.customer.regist')
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/ajax/customer.js') }}"></script>
@endsection
