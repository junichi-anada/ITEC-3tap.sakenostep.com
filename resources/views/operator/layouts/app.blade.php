@include('operator.layouts.include.header')

<!-- container -->
<div class="w-full mx-auto border overflow-x-hidden flex relative">

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

    @include('operator.layouts.include.side')

    <!-- contents -->
    <div class="flex flex-col w-full">

        <!-- contents-header -->
        <div class="flex justify-end items-center bg-[#F4CF41] gap-x-6 px-2">
            <p class="text-base">アイテックユーザー 様</p>
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

@include('operator.layouts.include.footer')

