@include('operator.layouts.include.page.header')

@include('operator.layouts.include.site.header')

<!-- container -->
<div class="w-full mx-auto border overflow-x-hidden flex relative">

    @include('operator.layouts.include.site.side')

    <!-- contents -->
    <div class="flex flex-col w-full">

        <!-- contents-header -->
        <div class="flex justify-end items-center bg-[#F4CF41] gap-x-6 px-2 relative">
            {{-- hamberger menu --}}
            <div class="relative w-[min(3vw, 40px)] aspect-square mr-auto" id="menu">
                <button id="hamburger" type="button" class="z-20">
                    <span id="bars" class="material-symbols-outlined hidden">menu</span>
                    <span id="xmark" class="material-symbols-outlined">close</span>
                </button>
            </div>
            {{-- //hamberger menu --}}
            <div class="flex flex-row items-center gap-x-2 flex-nowrap">
                <span class="material-symbols-outlined">person</span>
                <p class="text-base">{{ $operator->name }} 様</p>
            </div>
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

{{-- 各ページに必要なjs --}}
@yield('js')

@include('operator.layouts.include.site.footer')

@include('operator.layouts.include.page.footer')
