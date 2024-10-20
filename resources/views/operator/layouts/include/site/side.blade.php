{{-- Sidebar --}}
<div class="w-full max-w-[200px] h-[calc(100vh-2.25rem)] bg-[#F8F8F8] relative hidden md:block">
    <div class="flex flex-col justify-center gap-y-5 pt-3">
        <a href="{{ route('user.order.item.list') }}" class="tracking-widest">
            <div class="w-32 mx-auto">
                <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
            </div>
        </a>
        <ul class="flex flex-col gap-y-1 pb-3 px-2">
            <li class="py-2 px-2">
                <a href="{{ route('operator.customer.list') }}" class="flex items-center w-full gap-x-4">
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

