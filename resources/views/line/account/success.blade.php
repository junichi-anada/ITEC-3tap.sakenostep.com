<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント連携成功</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">連携完了</h2>
                <p class="mt-2 text-gray-600">LINEアカウントとの連携が完了しました。</p>
                <div class="mt-6">
                    <p class="text-sm text-gray-500">このページは自動的に閉じられます。</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        // 3秒後にウィンドウを閉じる
        setTimeout(() => {
            window.close();
        }, 3000);
    </script>
</body>
</html>
