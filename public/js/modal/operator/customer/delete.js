/**
 * 顧客登録確認モーダルの表示
 */

// モーダルの開閉を制御するJavaScript
// 表示処理

export function makeCustomerDeleteConfirmModal() {
    const CustomerDeleteConfirmModal = document.getElementById("modal");
    if (CustomerDeleteConfirmModal) {
        // #modalの中にある#modaleTitleと#modalContentにテキストを追加
        document.getElementById("modalTitle").textContent = "削除確認";
        document.getElementById("modalContent").innerHTML =
            "顧客情報を削除してもよろしいですか？";
        document.getElementById("execModal").style.display = "block";
        document.getElementById("execModal").textContent = "顧客情報を削除する";
        document.getElementById("cancelModal").textContent = "キャンセル";
        document.getElementById("execMode").textContent = "delete";
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

// 削除完了モーダルの表示
export function makeCustomerDeleteSuccessModal() {
    const CustomerDeleteCompleteModal = document.getElementById("modal");
    if (CustomerDeleteCompleteModal) {
        document.getElementById("modalTitle").textContent = "削除完了";
        document.getElementById("modalContent").innerHTML =
            "顧客情報を削除しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}

// 削除失敗モーダルの表示
export function makeCustomerDeleteFailModal() {
    const CustomerDeleteFailModal = document.getElementById("modal");
    if (CustomerDeleteFailModal) {
        document.getElementById("modalTitle").textContent = "削除失敗";
        document.getElementById("modalContent").innerHTML =
            "顧客情報の削除に失敗しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}
