@include('user.layouts.include.header')

<!-- container -->
<div class="w-full min-w-[360px] max-w-[420px] mx-auto border border-gray-300 overflow-x-hidden relative">

    @include('user.layouts.site.header')

    <!-- nav -->
    <div class="bg-[#F4CF41] absolute px-5 py-2 -left-full top-0 transition-all ease-linear"
        id="menu">
        <div>
            <div class="w-[22px]">
                <img src="{{ asset('image/user/mymenu/svg/line.svg') }}" alt="LINE">
            </div>
        </div>
        <ul class="flex flex-col gap-y-1 pb-3">
            <li class="border-b border-gray-400 py-2 px-2">
                <a href="#" class="inline-block w-full"><span class="text-base">注文履歴</span></a>
            </li>
            <li class="border-b border-gray-400 pb-2 px-2">
                <a href="#" class="inline-block  w-full"><span class="text-base">商品一覧</span></a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">日本酒</span>
                </a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">焼酎</span>
                </a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">ワイン</span>
                </a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">ビール</span>
                </a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">ウイスキー</span>
                </a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">リキュール</span>
                </a>
            </li>
            <li class="indent-2 border-b border-gray-400 pb-1">
                <a href="#" class="inline-block w-full">
                    <span class="text-sm">その他</span>
                </a>
            </li>
        </ul>
        <div class="flex justify-center gap-x-4">
            <div>
                <a href="" class="bg-white flex flex-col items-center border border-gray-300 gap-y-3 py-3 px-3">
                    <span class="material-symbols-outlined text-3xl">description</span>
                    <span class="text-xs">注文について</span>
                </a>
            </div>
            <div>
                <a href="" class="bg-white flex flex-col items-center border border-gray-300 gap-y-3 py-3 px-3">
                    <span class="material-symbols-outlined text-3xl">local_shipping</span>
                    <span class="text-xs">配送について</span>
                </a>
            </div>
        </div>
    </div>
    <!-- //nav -->


    @include('user.layouts.widgets.search')

    @include('user.layouts.widgets.tabnavi')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- content -->
    <div class="flex flex-col pb-2 px-2 h-[calc(100vh-(4rem+6rem+2px+60px))] bg-[#F6F6F6]">
        @yield('content')
    </div>
    <!-- //content -->

    @include('user.layouts.site.footer')

    @include('user.layouts.widgets.copyright')

    @include('user.layouts.include.footer')


