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
    <div class="flex flex-col justify-center items-center gap-y-8" style="height: 100vh;">
        <div>
            <img src="{{ asset('image/3tap-step.png') }}" alt="酒のステップ" style="max-width: 500px;">
        </div>
        <a href="{{ route('customer.index') }}" class="px-6 py-3 bg-[#F4CF41] hover:bg-[#E4BF31] text-gray-800 font-semibold rounded-md shadow-md transition-colors duration-300">
            お取引業者様はこちら
        </a>
    </div>
</body>

</html>
