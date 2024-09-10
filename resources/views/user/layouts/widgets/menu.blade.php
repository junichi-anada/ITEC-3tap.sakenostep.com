<!-- hamberger menu -->
<div class="absolute top-1 left-2">
    <button id="hamburger" type="button" class="fixed z-20">
        <span id="bars" class="material-symbols-outlined">menu </span>
        <span id="xmark" class="material-symbols-outlined hidden">close</span>
    </button>
</div>
<!-- //hamberger menu -->

<!-- nav -->
<div class="bg-[#F4CF41] absolute px-5 py-2 top-0 transition-all ease-linear -translate-x-full"
    id="menu">
    <div>
        <div class="w-[22px] ml-auto">
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

<!-- Script -->
<script src="{{ asset('js/menu.js') }}"></script>

