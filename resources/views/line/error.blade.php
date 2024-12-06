<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LINE連携エラー</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            color: #dc3545;
            font-size: 48px;
            margin-bottom: 1rem;
        }
        .message {
            color: #333;
            margin-bottom: 1.5rem;
        }
        .close-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .close-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">×</div>
        <h1 class="message">{{ session('error', 'LINE連携に失敗しました') }}</h1>
        <button class="close-button" onclick="window.close()">閉じる</button>
    </div>
</body>
</html>
