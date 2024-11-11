@include('customer.layouts.include.page.header')

{{-- container --}}
<div class="w-full min-w-[360px] max-w-[420px] mx-auto border border-gray-300 overflow-x-hidden relative">

    @include('customer.layouts.include.site.header')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- content --}}
    <div class="flex flex-col pb-2 px-2 h-[calc(100vh-(4rem+6rem+2px+60px))] bg-[#F6F6F6]">

        <!-- Items -->
        <div class="overflow-y-auto bg-white py-3 px-3 h-full">
            <div class="flex flex-col gap-y-3">
                @yield('contents')
            </div>
        </div>
        <!-- //Items -->

    </div>
    {{-- //content --}}

    @include('customer.layouts.include.site.footer')

    @include('customer.layouts.include.page.footer')
