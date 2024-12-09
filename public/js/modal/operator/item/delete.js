// モーダル関連の要素
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modalTitle");
const modalBody = document.getElementById("modalBody");
const execModal = document.getElementById("execModal");
const cancelModal = document.getElementById("cancelModal");
const execMode = document.getElementById("execMode");

// 削除確認モーダルの表示
export function makeItemDeleteConfirmModal() {
    modalTitle.textContent = "商品削除の確認";
    modalBody.textContent = "この商品情報を削除してもよろしいですか？";
    execModal.textContent = "削除する";
    execModal.classList.remove("bg-[#F4CF41]");
    execModal.classList.add("bg-red-500", "text-white");
    execMode.textContent = "delete";
    modal.classList.remove("hidden");
}

// 削除成功モーダルの表示
export function makeItemDeleteSuccessModal() {
    modalTitle.textContent = "削除完了";
    modalBody.textContent = "商品情報の削除が完了しました。";
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");

    // 3秒後に一覧画面に遷移
    setTimeout(() => {
        window.location.href = "/operator/item";
    }, 3000);
}

// 削除失敗モーダルの表示
export function makeItemDeleteFailModal() {
    modalTitle.textContent = "エラー";
    modalBody.textContent = "商品情報の削除に失敗しました。";
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
    execModal.classList.remove("bg-red-500", "text-white");
    execModal.classList.add("bg-[#F4CF41]");
    execModal.classList.remove("hidden");
}
