/**
 * 注文確認モーダルの表示
 */

// モーダルの開閉を制御するJavaScript
// 表示処理
document
    .getElementById("openOrderModal")
    .addEventListener("click", function () {
        document.getElementById("modalTitle").textContent = "注文確認";
        document.getElementById("modalContent").textContent =
            "注文確認の文章が入ります。";
        document.getElementById("modal").classList.remove("hidden");
        // #modalの中にある#modaleTitleと#modalContentにテキストを追加
    });

document.getElementById("closeModal").addEventListener("click", function () {
    document.getElementById("modal").classList.add("hidden");
});

// モーダルの背景をクリックしても閉じるようにする
document.getElementById("modal").addEventListener("click", function (e) {
    if (e.target === this) {
        this.classList.add("hidden");
    }
});
