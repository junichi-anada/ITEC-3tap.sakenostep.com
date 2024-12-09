<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント連携エラー</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">連携エラー</h2>
                <p class="mt-2 text-gray-600">LINEアカウントとの連携に失敗しました。</p>
                <div class="mt-6">
                    <p class="text-sm text-gray-500">
                        お手数ですが、もう一度お試しいただくか、<br>
                        サポートまでお問い合わせください。
                    </p>
                </div>
                <div class="mt-6">
                    <button onclick="window.close()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                        閉じる
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
