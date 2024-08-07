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

    <!-- font-awesome -->
    <script src="https://kit.fontawesome.com/de1548f7bd.js" crossorigin="anonymous"></script>

    @vite('resources/css/app.css')

</head>

<body class="font-body">
    <!-- container -->
    <div class="w-full max-w-[400px] mx-auto border border-gray-300 overflow-x-hidden relative">

        <!-- header -->
        <div class="bg-[#F4CF41] py-1 relative">
            <div class="flex justify-center">
                <a href="./" class="tracking-widest text-xl font-extrabold">
                    <span class=" text-[#DC2626]">酒</span><span class="text-sm text-[#DC2626]">の</span>ステップ
                </a>
            </div>

            <!-- menu -->
            <div class="absolute top-1 right-6">
                <button id="hamburger" type="button" class="fixed z-20">
                    <i id="bars" class="fa-solid fa-bars fa-lg"></i>
                    <i id="xmark" class="fa-solid fa-xmark fa-lg hidden text-black"></i>
                </button>
            </div>
            <!-- //menu -->

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
                        <div class="w-[22px]">
                            <img src="{{ asset('image/user/mymenu/svg/search.svg') }}" alt="">
                        </div>
                        <span class="text-xs">注文について</span>
                    </a>
                </div>
                <div>
                    <a href="" class="bg-white flex flex-col items-center border border-gray-300 gap-y-3 py-3 px-3">
                        <div class="w-[22px]">
                            <img src="{{ asset('image/user/mymenu/svg/search.svg') }}" alt="">
                        </div>
                        <span class="text-xs">配送について</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- //nav -->


        <!-- content -->
        <div class="flex flex-col py-2 px-2 h-[calc(100vh-(3rem+8rem+2px))] bg-[#F6F6F6]">
            <!-- Search Window -->
            <div class="flex items-center justify-between border border-gray-400 mb-2">
                <button class="w-[30px] bg-white py-2 px-2">
                    <img src="{{ asset('image/user/mymenu/svg/search.svg') }}" alt="">
                </button>
                <input type="text" class="text-sm w-full py-1 pl-2" placeholder="商品名・商品コードで検索">
            </div>
            <!-- //Search Window -->

            <!-- Tab -->
            <div class="overflow-x-scroll hide-scrollbar h-auto">
                <div class="flex flex-nowrap">
                    <a href="#" class="py-2 px-3 whitespace-nowrap block bg-white rounded-t-xl">
                        <div class="flex items-center gap-x-2">
                            <div class="w-[12px]">
                                <img src="{{ asset('image/user/mymenu/svg/repeat.svg') }}" alt="" class="filter">
                            </div>
                            <span class="text-xs">注文リスト</span>
                        </div>
                    </a>
                    <a href="#" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                        <div class="flex items-center gap-x-2">
                            <div class="w-[12px]">
                                <img src="{{ asset('image/user/mymenu/svg/repeat.svg') }}" alt="">
                            </div>
                            <span class="text-xs">マイリスト</span>
                        </div>
                    </a>
                    <a href="#" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                        <div class="flex items-center gap-x-2">
                            <div class="w-[12px]">
                                <img src="{{ asset('image/user/mymenu/svg/repeat.svg') }}" alt="">
                            </div>
                            <span class="text-xs">おすすめ</span>
                        </div>
                    </a>
                    <a href="#" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                        <div class="flex items-center gap-x-2">
                            <div class="w-[12px]">
                                <img src="{{ asset('image/user/mymenu/svg/repeat.svg') }}" alt="">
                            </div>
                            <span class="text-xs">商品一覧</span>
                        </div>
                    </a>
                    <a href="#" class="py-2 px-3 whitespace-nowrap block bg-gray-300 rounded-t-lg">
                        <div class="flex items-center gap-x-2">
                            <div class="w-[12px]">
                                <img src="{{ asset('image/user/mymenu/svg/repeat.svg') }}" alt="">
                            </div>
                            <span class="text-xs">検索結果</span>
                        </div>
                    </a>
                </div>
            </div>
            <!-- //Tab -->

            <!-- Items -->
            <div class="overflow-y-auto bg-white py-3 px-3">
                <div class="flex flex-col gap-y-3">
                    <!-- Item -->
                    <div class="flex flex-col gap-y-4 border-b pb-3">
                        <p class="font-bold leading-5">
                            大七 純米生酛 生詰め<br>
                            <span class="text-xs font-normal">大七酒造</span>
                        </p>
                        <div class="flex justify-between">
                            <div>
                                <button class="border-2 border-red-500 px-2 py-1">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <div class="w-[18px]">
                                            <img src="{{ asset('image/user/mymenu/svg/delete.svg') }}" alt="リストから削除">
                                        </div>
                                        <span class="text-xs">リストから削除</span>
                                    </div>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <button class="border px-1.5 py-0.5 border-r-0 text-xs">－</button>
                                <input type="text" name="" value="2"
                                    class="w-16 border border-r-0 text-center py-0.5 text-xs">
                                <button class="border px-1.5 py-0.5 text-xs">＋</button>
                                <span class="inline-block ml-2 text-xs">本</span>
                            </div>
                        </div>
                    </div>
                    <!-- //Item -->

                    <!-- Item -->
                    <div class="flex flex-col gap-y-4 border-b pb-3">
                        <p class="font-bold leading-5">
                            大七 純米生酛 生詰め<br>
                            <span class="text-xs font-normal">大七酒造</span>
                        </p>
                        <div class="flex justify-between">
                            <div>
                                <button class="border-2 border-red-500 px-2 py-1">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <div class="w-[18px]">
                                            <img src="{{ asset('image/user/mymenu/svg/delete.svg') }}" alt="リストから削除">
                                        </div>
                                        <span class="text-xs">リストから削除</span>
                                    </div>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <button class="border px-1.5 py-0.5 border-r-0 text-xs">－</button>
                                <input type="text" name="" value="2"
                                    class="w-16 border border-r-0 text-center py-0.5 text-xs">
                                <button class="border px-1.5 py-0.5 text-xs">＋</button>
                                <span class="inline-block ml-2 text-xs">本</span>
                            </div>
                        </div>
                    </div>
                    <!-- //Item -->

                    <!-- Item -->
                    <div class="flex flex-col gap-y-4 border-b pb-3">
                        <p class="font-bold leading-5">
                            大七 純米生酛 生詰め<br>
                            <span class="text-xs font-normal">大七酒造</span>
                        </p>
                        <div class="flex justify-between">
                            <div>
                                <button class="border-2 border-red-500 px-2 py-0.5">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <div class="w-[18px]">
                                            <img src="{{ asset('image/user/mymenu/svg/delete.svg') }}" alt="リストから削除">
                                        </div>
                                        <span class="text-xs">リストから削除</span>
                                    </div>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <button class="border px-1.5 py-0.5 border-r-0 text-xs">－</button>
                                <input type="text" name="" value="2"
                                    class="w-16 border border-r-0 text-center py-0.5 text-xs">
                                <button class="border px-1.5 py-0.5 text-xs">＋</button>
                                <span class="inline-block ml-2 text-xs">本</span>
                            </div>
                        </div>
                    </div>
                    <!-- //Item -->

                    <!-- Item -->
                    <div class="flex flex-col gap-y-4 border-b pb-3">
                        <p class="font-bold leading-5">
                            大七 純米生酛 生詰め<br>
                            <span class="text-xs font-normal">大七酒造</span>
                        </p>
                        <div class="flex justify-between">
                            <div>
                                <button class="border-2 border-red-500 px-2 py-1">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <div class="w-[18px]">
                                            <img src="{{ asset('image/user/mymenu/svg/delete.svg') }}" alt="リストから削除">
                                        </div>
                                        <span class="text-xs">リストから削除</span>
                                    </div>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <button class="border px-1.5 py-0.5 border-r-0 text-xs">－</button>
                                <input type="text" name="" value="2"
                                    class="w-16 border border-r-0 text-center py-0.5 text-xs">
                                <button class="border px-1.5 py-0.5 text-xs">＋</button>
                                <span class="inline-block ml-2 text-xs">本</span>
                            </div>
                        </div>
                    </div>
                    <!-- //Item -->

                    <!-- Item -->
                    <div class="flex flex-col gap-y-4 border-b pb-3">
                        <p class="font-bold leading-5">
                            大七 純米生酛 生詰め<br>
                            <span class="text-xs font-normal">大七酒造</span>
                        </p>
                        <div class="flex justify-between">
                            <div>
                                <button class="border-2 border-red-500 px-2 py-1">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <div class="w-[18px]">
                                            <img src="{{ asset('image/user/mymenu/svg/delete.svg') }}" alt="リストから削除">
                                        </div>
                                        <span class="text-xs">リストから削除</span>
                                    </div>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <button class="border px-1.5 py-0.5 border-r-0 text-xs">－</button>
                                <input type="text" name="" value="2"
                                    class="w-16 border border-r-0 text-center py-0.5 text-xs">
                                <button class="border px-1.5 py-0.5 text-xs">＋</button>
                                <span class="inline-block ml-2 text-xs">本</span>
                            </div>
                        </div>
                    </div>
                    <!-- //Item -->

                    <!-- Item -->
                    <div class="flex flex-col gap-y-4 border-b pb-3">
                        <p class="font-bold leading-5">
                            大七 純米生酛 生詰め<br>
                            <span class="text-xs font-normal">大七酒造</span>
                        </p>
                        <div class="flex justify-between">
                            <div>
                                <button class="border-2 border-red-500 px-2 py-1">
                                    <div class="flex items-center gap-x-2 py-1">
                                        <div class="w-[18px]">
                                            <img src="{{ asset('image/user/mymenu/svg/delete.svg') }}" alt="リストから削除"
                                                class="filter">
                                        </div>
                                        <span class="text-xs">リストから削除</span>
                                    </div>
                                </button>
                            </div>
                            <div class="flex items-center">
                                <button class="border px-1.5 py-0.5 border-r-0 text-xs">－</button>
                                <input type="text" name="" value="2"
                                    class="w-16 border border-r-0 text-center py-0.5 text-xs">
                                <button class="border px-1.5 py-0.5 text-xs">＋</button>
                                <span class="inline-block ml-2 text-xs">本</span>
                            </div>
                        </div>
                    </div>
                    <!-- //Item -->

                </div>
            </div>
            <!-- //Items -->

            <!-- Control -->
            <div class="bg-transparent py-6">
                <div class="flex justify-center gap-x-16">
                    <button class="bg-red-600 text-white px-7 py-1.5  rounded-xl">
                        全て削除
                    </button>
                    <button class="bg-red-600 text-white px-6 py-1.5  rounded-xl">
                        注文する
                    </button>
                </div>
            </div>
        </div>
        <!-- //content -->

        <!-- footer -->
        <div class=" w-full mx-auto">
            <div class="flex">
                <a href="./history"
                    class="flex items-center w-1/2 text-center py-2 px-3 border border-black border-r-0">
                    <div class="w-[15%]">
                        <img src="{{ asset('image/user/mymenu/svg/history.svg') }}" alt="">
                    </div>
                    <p class="flex-grow font-normal">
                        ご注文履歴
                    </p>
                </a>
                <a href="./contact" class="flex items-center w-1/2 text-center py-2 px-3 border border-black">
                    <div class="w-[15%]">
                        <img src="{{ asset('image/user/mymenu/svg/phone.svg') }}" alt="">
                    </div>
                    <div class="flex-grow relative -top-1">
                        <p class="text-lg font-bold leading-5">
                            <span class="text-xs font-normal">お電話でのお問合せ</span><br>
                            9:00～20:00
                        </p>
                    </div>
                </a>
            </div>
            <div class="bg-[#F4CF41] pt-12 relative">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full">
                    <p class="font-extrabold text-sm text-center">
                        ©sakenostep.com All rights reserved
                    </p>
                </div>
            </div>
        </div>
        <!-- //footer -->

    </div>
    <!-- // container -->

    <!-- Script -->
    <script src="{{ asset('js/hamberger.js') }}"></script>
</body>

</html>
