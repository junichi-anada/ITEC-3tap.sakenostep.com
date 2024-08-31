<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3TAPシステム|酒のステップ</title>

    <!-- Adobe Font -->
    <script>
        (function (d) {
            var config = {
                kitId: 'pcv3mhw',
                scriptTimeout: 3000,
                async: true
            },
                h = d.documentElement, t = setTimeout(function () { h.className = h.className.replace(/\bwf-loading\b/g, "") + " wf-inactive"; }, config.scriptTimeout), tk = d.createElement("script"), f = false, s = d.getElementsByTagName("script")[0], a; h.className += " wf-loading"; tk.src = 'https://use.typekit.net/' + config.kitId + '.js'; tk.async = true; tk.onload = tk.onreadystatechange = function () { a = this.readyState; if (f || a && a != "complete" && a != "loaded") return; f = true; clearTimeout(t); try { Typekit.load(config) } catch (e) { } }; s.parentNode.insertBefore(tk, s)
        })(document);
    </script>

    <!-- Google Font Icon -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    @vite('resources/css/app.css')

</head>

<body class="font-body">
    <!-- container -->
    <div class="w-full max-w-[480px] mx-auto border border-gray-300 overflow-x-hidden relative">

        <!-- header -->
        <div class="bg-[#F4CF41] py-1 relative">
            <!-- logout -->
            <div class="absolute top-1 left-2">
                <a href="./logout"><span class="material-symbols-outlined">logout</span></a>
            </div>
            <!-- //logout -->

            <div class="flex justify-center">
                <a href="./" class="tracking-widest text-xl font-extrabold">
                    <span class=" text-[#DC2626]">酒</span><span class="text-sm text-[#DC2626]">の</span>ステップ
                </a>
            </div>

            <!-- hamberger menu -->
            <div class="absolute top-1 right-8">
                <button id="hamburger" type="button" class="fixed z-20">
                    <span id="bars" class="material-symbols-outlined">menu </span>
                    <span id="xmark" class="material-symbols-outlined hidden">close</span>
                </button>
            </div>
            <!-- //hamberger menu -->

        </div>
        <!-- //header -->

        <!-- nav -->
        <div class="bg-[#F4CF41] absolute px-5 py-2 right-0 top-0 transition-all ease-linear translate-x-full"
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

        <!-- Search Window Wrapper -->
        <div class="flex flex-col pt-2 px-2 bg-[#F6F6F6]">
            <div class="flex items-center justify-between border border-gray-400">
                <button class="w-[30px] bg-white py-0.5 px-2">
                    <span class="material-symbols-outlined text-xl font-bold">search</span>
                </button>
                <input type="text" class="text-sm w-full py-1.5 pl-2" placeholder="商品名・商品コードで検索">
            </div>
        </div>
        <!-- //Search Window Wrapper -->

        <!-- Tab Navi Wrapper -->
        <div class="flex flex-col pt-2 px-2 bg-[#F6F6F6]">
            <div class="h-[44px]">
                <div class="overflow-x-scroll hide-scrollbar">
                    <div class="flex flex-nowrap">
                        <a href="./order" class="py-2 px-3 whitespace-nowrap block {{ request()->is('order') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                            <div class="flex items-center gap-x-1">
                                <span class="material-symbols-outlined text-xl text-[#F4CF41]">repeat</span>
                                <span class="text-xs">注文リスト</span>
                            </div>
                        </a>
                        <a href="./favorites" class="py-2 px-3 whitespace-nowrap block {{ request()->is('favorites') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                            <div class="flex items-center gap-x-1">
                                <span class="material-symbols-outlined text-xl text-[#F4CF41]">star</span>
                                <span class="text-xs">マイリスト</span>
                            </div>
                        </a>
                        <a href="./recommendations" class="py-2 px-3 whitespace-nowrap block {{ request()->is('recommendations') ? 'bg-white rounded-t-xl': 'bg-gray-300 rounded-t-lg' }}">
                            <div class="flex items-center gap-x-2">
                                <span class="material-symbols-outlined text-xl text-[#F4CF41]">thumb_up</span>
                                <span class="text-xs">おすすめ</span>
                            </div>
                        </a>
                        <a href="#" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                            <div class="flex items-center gap-x-2">
                                <span
                                    class="material-symbols-outlined text-xl text-[#F4CF41]">format_list_bulleted</span>
                                <span class="text-xs">商品一覧</span>
                            </div>
                        </a>
                        <a href="#" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                            <div class="flex items-center gap-x-2">
                                <span class="material-symbols-outlined text-xl text-[#F4CF41]">search</span>
                                <span class="text-xs">検索結果</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- //Tab Navi Wrapper -->



