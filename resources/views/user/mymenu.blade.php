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
    <div class="w-full max-w-[375px] mx-auto border border-gray-300">

        <!-- header -->
        <div class="bg-[#F4CF41] pt-8 relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full">
                <p class="font-extrabold text-center text-xl tracking-widest">
                    <span class="text-[#DC2626]">酒</span><span class="text-lg text-[#DC2626]">の</span>ステップ
                </p>
            </div>
        </div>
        <!-- //header -->

        <!-- content -->
        <div class="flex flex-col py-8 px-6 gap-y-6  h-[calc(100vh-(6rem+4rem+2px))]">


        </div>
        <!-- //content -->

        <!-- footer -->
        <div class="w-full max-w-[375px] mx-auto">
            <div class="flex">
                <div>
                    <a href="" class=" text-[#F4CF41]">history</a>
                </div>
                <div>
                    <a href="" class=""></a>
                </div>
            </div>
            <div class="bg-[#F4CF41] pt-16 relative">
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
