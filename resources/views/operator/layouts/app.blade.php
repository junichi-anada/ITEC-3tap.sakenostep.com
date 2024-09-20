@include('operator.layouts.include.page.header')

@include('operator.layouts.include.site.header')

<!-- container -->
<div class="w-full mx-auto border overflow-x-hidden flex relative">

    @include('operator.layouts.include.site.side')

    <!-- contents -->
    <div class="flex flex-col w-full">

        <!-- contents-header -->
        <div class="flex justify-end items-center bg-[#F4CF41] gap-x-6 px-2">
            <p class="text-base">{{ $operator->name }} 様</p>
            <a href="{{ route('logout') }}"><span class="material-symbols-outlined text-3xl">logout</span></a>
        </div>
        <!-- //contents-header -->

        <!-- contents-body -->
        <div class="px-5 py-5">
            @yield('content')
        </div>
        <!-- //contents-body -->
    </div>
    <!-- //contents -->

</div>
<!-- // container -->

@include('operator.layouts.include.site.footer')

@include('operator.layouts.include.page.footer')
