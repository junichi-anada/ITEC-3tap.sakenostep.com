    <!-- Sidebar -->
    <div class="w-full max-w-[250px] h-[calc(100vh-2.25rem)] bg-[#F8F8F8]">
        <!-- hamberger menu -->
        {{-- <div class="absolute top-1 right-8">
            <button id="hamburger" type="button" class="fixed z-20">
                <span id="bars" class="material-symbols-outlined">menu </span>
                <span id="xmark" class="material-symbols-outlined hidden">close</span>
            </button>
        </div> --}}
        <!-- //hamberger menu -->

        <div class="flex flex-col justify-center gap-y-5 pt-5">
            <a href="{{ route('user.order.item.list') }}" class="tracking-widest text-xl font-extrabold">
                <div class="w-48 mx-auto">
                    <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
                </div>
            </a>
            <ul class="flex flex-col gap-y-1 pb-3 px-2">
                <li class="py-4 px-2">
                    <a href="#" class="flex items-center w-full gap-x-4">
                        <span class="material-symbols-outlined text-4xl">person</span>
                        <span class="text-xl">顧客管理</span>
                    </a>
                </li>
                <li class="py-4 px-2">
                    <a href="#" class="flex items-center w-full gap-x-4">
                        <span class="material-symbols-outlined text-4xl">description</span>
                        <span class="text-xl">注文管理</span>
                    </a>
                </li>
                <li class="py-4 px-2">
                    <a href="#" class="flex items-center w-full gap-x-4">
                        <span class="material-symbols-outlined text-4xl">liquor</span>
                        <span class="text-xl">商品管理</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
    <!-- Sidebar -->

    <!-- Script -->
    <script src="{{ asset('js/hamberger.js') }}"></script>

