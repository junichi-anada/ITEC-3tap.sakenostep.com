/**
 * 注文確認モーダルの表示
 */

// モーダルの開閉を制御するJavaScript
// 表示処理

export function makeOrderConfirmModal() {
    const orderConfirmModal = document.getElementById("modal");
    if (orderConfirmModal) {
        // #modalの中にある#modaleTitleと#modalContentにテキストを追加
        document.getElementById("modalTitle").textContent = "注文確認";
        document.getElementById("modalContent").innerHTML =
            "注文リストの内容を送信します。<br>よろしいですか？";
        document.getElementById("execModal").textContent = "注文する";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }

    const cancelModal = document.getElementById("cancelModal");
    if (cancelModal) {
        cancelModal.addEventListener("click", function () {
            document.getElementById("modal").classList.add("hidden");
        });
    }

    // モーダルの背景をクリックしても閉じるようにする
    const modal = document.getElementById("modal");
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                this.classList.add("hidden");
            }
        });
    }
}
