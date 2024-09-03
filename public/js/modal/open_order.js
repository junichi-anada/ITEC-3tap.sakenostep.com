/**
 * 注文確認モーダルの表示
 */

// モーダルの開閉を制御するJavaScript
// 表示処理
document
    .getElementById("openOrderModal")
    .addEventListener("click", function () {
        // #modalの中にある#modaleTitleと#modalContentにテキストを追加
        document.getElementById("modalTitle").textContent = "注文確認";
        document.getElementById("modalContent").innerHTML =
            "注文リストの内容を送信します。<br>よろしいですか？";
        document.getElementById("execModal").textContent = "注文する";
        document.getElementById("cancelModal").textContent = "閉じる";

        document.getElementById("modal").classList.remove("hidden");
    });

document.getElementById("cancelModal").addEventListener("click", function () {
    document.getElementById("modal").classList.add("hidden");
});

// モーダルの背景をクリックしても閉じるようにする
document.getElementById("modal").addEventListener("click", function (e) {
    if (e.target === this) {
        this.classList.add("hidden");
    }
});
