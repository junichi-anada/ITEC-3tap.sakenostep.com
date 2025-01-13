<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3TAPシステム|酒のステップ</title>
    @vite('resources/css/app.css')

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
</head>

<body class="font-body">
    <!-- container -->
    <div class="w-full min-w-[360px] max-w-[420px] mx-auto border border-gray-300">

        <!-- header(top only) -->
        <div class="bg-[#F4CF41] pt-24 relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                <div class="flex flex-col items-center gap-y-1">
                    <div class="w-32">
                        <img src="{{ asset('image/step_logo.png') }}" alt="酒のステップ">
                    </div>
                    <p class="font-extrabold text-center text-2xl tracking-widest">
                        エラーが発生しました
                    </p>
                </div>
            </div>
        </div>
        <!-- //header -->

        <!-- content -->
        <div class="h-[calc(100vh-(2rem+6rem+2px))]">
            <div class="flex flex-col py-8 px-6 gap-y-6">
                <div class="text-center">
                    <p class="text-gray-600 mb-6">
                        {{ session('error', 'アカウント連携処理中にエラーが発生しました。') }}
                    </p>
                    <p class="text-sm text-gray-500 mb-8">
                        お手数ですが、もう一度お試しいただくか、<br>
                        サポートまでお問い合わせください。
                    </p>
                    <button onclick="window.close()" 
                        class="bg-[#F4CF41] text-black rounded-lg px-8 py-2 font-bold">
                        閉じる
                    </button>
                </div>
            </div>
        </div>
        <!-- //content -->

        <!-- footer -->
        <div class="w-full mx-auto">
            <div class="bg-[#F4CF41] pt-8 relative">
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
</body>

</html>
