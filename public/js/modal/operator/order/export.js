// モーダル関連の要素
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modalTitle");
const modalBody = document.getElementById("modalBody");
const execModal = document.getElementById("execModal");
const cancelModal = document.getElementById("cancelModal");
const execMode = document.getElementById("execMode");

// CSV書出確認モーダルの表示
export function makeOrderExportConfirmModal() {
    modalTitle.textContent = "CSV書出の確認";
    modalBody.textContent = "注文データをCSVファイルとして書き出しますか？";
    execModal.textContent = "書出開始";
    execMode.textContent = "export";
    modal.classList.remove("hidden");
}

// CSV書出成功モーダルの表示
export function makeOrderExportSuccessModal() {
    modalTitle.textContent = "CSV書出完了";
    modalBody.textContent = "注文データのCSV書出が完了しました。";
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");

    // 3秒後に画面をリロード
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

// CSV書出失敗モーダルの表示
export function makeOrderExportFailModal(
    message = "注文データのCSV書出に失敗しました。"
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
