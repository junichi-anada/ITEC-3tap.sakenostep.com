/**
 * ログインボタン押下時の処理
 *
 * index.blade.phpのログインボタン押下時に呼び出される
 * ログイン処理をAjaxで行い、結果に応じてリダイレクトをかける。
 * ログイン成功時はtokenとリダイレクト先のURLが戻ってくるので、
 * tokenをlocalStorageに保存する。
 * ログイン失敗時はエラーメッセージが戻ってくるので、エラーメッセージを表示する。
 */

// イベントリスナー登録
document.getElementById("login").addEventListener("click", function () {
    var loginCode = document.querySelector('input[name="login_code"]').value;
    var password = document.querySelector('input[name="password"]').value;
    var siteCode = document.querySelector('input[name="site_code"]').value;

    fetch("/api/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            login_code: loginCode,
            password: password,
            site_code: siteCode,
        }),
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error("ネットワークレスポンスが異常です。");
            }
            return response.json();
        })
        .then(function (data) {
            if (data.token) {
                localStorage.setItem("token", data.token);
                window.location.href = data.redirect_url;
            } else {
                console.log(data);
                alert("ログインに失敗しました。");
            }
        })
        .catch(function (error) {
            alert("エラー - " + error.message);
        });
});
