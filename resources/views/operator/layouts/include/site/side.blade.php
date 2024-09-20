{{-- Sidebar --}}
<div class="w-full max-w-[200px] h-[calc(100vh-2.25rem)] bg-[#F8F8F8] relative">
    {{-- hamberger menu --}}
    {{-- <div class="absolute top-3 right-8" id="menu">
        <button id="hamburger" type="button" class="fixed z-20">
            <span id="bars" class="material-symbols-outlined hidden">menu </span>
            <span id="xmark" class="material-symbols-outlined">close</span>
        </button>
    </div> --}}
    {{-- //hamberger menu --}}

    <div class="flex flex-col justify-center gap-y-5 pt-3">
        <a href="{{ route('user.order.item.list') }}" class="tracking-widest">
            <div class="w-32 mx-auto">
                <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
            </div>
        </a>
        <ul class="flex flex-col gap-y-1 pb-3 px-2">
            <li class="py-2 px-2">
                <a href="#" class="flex items-center w-full gap-x-4">
                    <span class="material-symbols-outlined text-3xl">person</span>
                    <span class="text-base">顧客管理</span>
                </a>
            </li>
            <li class="py-2 px-2">
                <a href="#" class="flex items-center w-full gap-x-4">
                    <span class="material-symbols-outlined text-3xl">description</span>
                    <span class="text-base">注文管理</span>
                </a>
            </li>
            <li class="py-2 px-2">
                <a href="#" class="flex items-center w-full gap-x-4">
                    <span class="material-symbols-outlined text-3xl">liquor</span>
                    <span class="text-base">商品管理</span>
                </a>
            </li>
        </ul>
    </div>
</div>
{{-- Sidebar --}}


{{-- Script --}}
<script src="{{ asset('js/menu.js') }}"></script>

