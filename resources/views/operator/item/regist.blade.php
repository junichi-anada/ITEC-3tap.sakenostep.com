@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-y-4">
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <x-operator.widgets.item.item-registration-form-component />
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/ajax/operator/item.js') }}"></script>
@endsection
