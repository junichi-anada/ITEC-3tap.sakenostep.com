@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-x-8 justify-start h-[calc(100vh-7.5rem)] md:flex-row">
        <div class="flex flex-col gap-y-2 w-full md:w-auto">
            <x-operator.widgets.item.item-search-form-component />
            <x-operator.widgets.item.item-toolbox-component />
        </div>
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <x-operator.widgets.item.item-list-component :items="$items" />
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/ajax/operator/item.js') }}"></script>
@endsection
