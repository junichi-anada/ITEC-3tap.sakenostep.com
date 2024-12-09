@extends('operator.layouts.app')

@section('content')
    <div class="flex flex-col gap-y-4">
        <div class="flex flex-col gap-y-4 md:flex-grow">
            <x-operator.widgets.customer.customer-import-progress-component
                :operator="$operator"
                :task="$task"
            />
        </div>
    </div>
@endsection

@push('scripts')
<script src="/js/modal/operator/customer/import.js"></script>
@endpush
