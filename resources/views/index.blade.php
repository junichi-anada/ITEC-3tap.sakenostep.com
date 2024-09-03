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
                        {{-- <span class="text-[#DC2626]">酒</span><span class="text-sm text-[#DC2626]">の</span>ステップ<br> --}}
                        オンライン注文
                    </p>
                </div>
            </div>
        </div>
        <!-- //header -->

        <!-- content -->
        <div class="h-[calc(100vh-(4rem+6rem+2px+60px))]">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="flex flex-col py-8 px-6 gap-y-6">
                    <div>
                        <label>■ 当社にご登録の電話番号</label>
                        <input type="text" class="border border-[#F4CF41] rounded-lg p-2 mt-1 font-bold w-full" name="login_code" value="test_user"  required autofocus>
                    </div>
                    <div>
                        <label>■ お客様番号</label>
                        <input type="text" class="border border-[#F4CF41] rounded-lg p-2 mt-1 font-bold w-full" name="password" value="test_user" required>
                    </div>
                    @error('login_code')
                    <div>
                            <span class="invalid-feedback text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                    </div>
                    @enderror
                    <input type="hidden" name="site_code" value="SITE-UUID-1234-5678">
                    <button class="bg-[#F4CF41] text-black rounded-lg p-2 mt-4 font-bold">ログイン</button>
                </div>
            </form>
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
