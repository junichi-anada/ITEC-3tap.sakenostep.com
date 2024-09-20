/**
 * 注文確認モーダルの表示
 */

// モーダルの開閉を制御するJavaScript
// 表示処理
document.getElementById("testModal").addEventListener("click", function () {
    // #modalの中にある#modaleTitleと#modalContentにテキストを追加
    document.getElementById("modalTitle").textContent = "注文確認";
    document.getElementById("modalContent").innerHTML = "テストです。";
    document.getElementById("execModal").textContent = "テストで開きます。";
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
