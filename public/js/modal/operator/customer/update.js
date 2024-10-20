/**
 * 顧客更新確認モーダルの表示
 */

// 更新確認モーダルの表示
export function makeCustomerUpdateConfirmModal() {
    const CustomerUpdateConfirmModal = document.getElementById("modal");
    if (CustomerUpdateConfirmModal) {
        // #modalの中にある#modaleTitleと#modalContentにテキストを追加
        document.getElementById("modalTitle").textContent = "更新確認";
        document.getElementById("modalContent").innerHTML =
            "顧客情報を更新してもよろしいですか？";
        document.getElementById("execModal").textContent = "顧客情報を更新する";
        document.getElementById("cancelModal").textContent = "キャンセル";
        document.getElementById("execMode").textContent = "update";
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

// 更新完了モーダルの表示
export function makeCustomerUpdateSuccessModal() {
    const CustomerUpdateCompleteModal = document.getElementById("modal");
    if (CustomerUpdateCompleteModal) {
        document.getElementById("modalTitle").textContent = "更新完了";
        document.getElementById("modalContent").innerHTML =
            "顧客情報を更新しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}

// 更新失敗モーダルの表示
export function makeCustomerUpdateFailModal() {
    const CustomerUpdateFailModal = document.getElementById("modal");
    if (CustomerUpdateFailModal) {
        document.getElementById("modalTitle").textContent = "更新失敗";
        document.getElementById("modalContent").innerHTML =
            "顧客情報の更新に失敗しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}
