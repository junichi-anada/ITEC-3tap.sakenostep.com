@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-y-4">
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <x-operator.widgets.customer.customer-edit-form-component :user="$user" :authenticate="$authenticate" />
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('js/validation/customer.js') }}"></script>
@endsection
