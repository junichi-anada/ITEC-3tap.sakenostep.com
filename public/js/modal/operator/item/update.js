// モーダル関連の要素
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modalTitle");
const modalBody = document.getElementById("modalBody");
const execModal = document.getElementById("execModal");
const cancelModal = document.getElementById("cancelModal");
const execMode = document.getElementById("execMode");

// 更新確認モーダルの表示
export function makeItemUpdateConfirmModal() {
    modalTitle.textContent = "商品情報更新の確認";
    modalBody.textContent = "この内容で更新してもよろしいですか？";
    execModal.textContent = "更新する";
    execMode.textContent = "update";
    modal.classList.remove("hidden");
}

// 更新成功モーダルの表示
export function makeItemUpdateSuccessModal() {
    modalTitle.textContent = "更新完了";
    modalBody.textContent = "商品情報の更新が完了しました。";
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");

    // 3秒後に画面をリロード
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

// 更新失敗モーダルの表示
export function makeItemUpdateFailModal(
    message = "商品情報の更新に失敗しました。"
) {
    modalTitle.textContent = "エラー";
    modalBody.textContent = message;
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");
}

// モーダルのキャンセルボタンクリックイベント
cancelModal?.addEventListener("click", function () {
    hideModal();
});

// モーダルを非表示
function hideModal() {
    modal.classList.add("hidden");
    execModal.classList.remove("hidden");
}
