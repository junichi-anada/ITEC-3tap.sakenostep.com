<!-- header -->
<div class="bg-[#F4CF41] py-1 relative">
    <!-- logout -->
    <div class="absolute top-1 right-2">
        <a href="{{ route('logout') }}"><span class="material-symbols-outlined">logout</span></a>
    </div>
    <!-- //logout -->

    <div class="flex justify-center">
        <a href="{{ route('user.order.item.list') }}" class="tracking-widest text-xl font-extrabold">
            <div class="w-32">
                <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
            </div>
            {{-- <span class=" text-[#DC2626]">酒</span><span class="text-sm text-[#DC2626]">の</span>ステップ --}}
        </a>
    </div>

    <!-- hamberger menu -->
    <div class="absolute top-1 left-2">
        <button id="hamburger" type="button" class="fixed z-20">
            <span id="bars" class="material-symbols-outlined">menu </span>
            <span id="xmark" class="material-symbols-outlined hidden">close</span>
        </button>
    </div>
    <!-- //hamberger menu -->

</div>
<!-- //header -->

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 max-w-md">
        <h2 class="text-2xl font-bold mb-4" id="modalTitle">モーダルタイトル</h2>
        <p class="mb-4" id="modalContent">これはモーダルの内容です。ここに必要な情報や操作を追加してください。</p>
        <div class="flex justify-center gap-x-8">
            <button id="execModal" class="bg-red-500 text-white px-4 py-2 rounded">
                注文する
            </button>
            <button id="cancelModal" class="bg-red-500 text-white px-4 py-2 rounded">
                閉じる
            </button>
        </div>
    </div>
</div>
<!-- //Modal -->
