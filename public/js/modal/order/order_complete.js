/**
 * 注文完了モーダルの表示
 */

// モーダルの開閉を制御するJavaScript
// 表示処理

export function makeOrderCompleteModal() {
    const makeOrderCompleteModal = document.getElementById("modal");
    if (makeOrderCompleteModal) {
        makeOrderCompleteModal.addEventListener("click", function () {
            // #modalの中にある#modaleTitleと#modalContentにテキストを追加
            document.getElementById("modalTitle").textContent = "注文完了";
            document.getElementById("modalContent").innerHTML =
                "注文を送信しました。<br>ご注文ありがとうございました。";
            document.getElementById("execModal").style.display = "none";
            document.getElementById("cancelModal").textContent = "閉じる";

            document.getElementById("modal").classList.remove("hidden");
        });
    }

    const cancelModal = document.getElementById("cancelModal");
    if (cancelModal) {
        cancelModal.addEventListener("click", function () {
            document.getElementById("modal").classList.add("hidden");
            // ページのリロード
            location.reload();
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
