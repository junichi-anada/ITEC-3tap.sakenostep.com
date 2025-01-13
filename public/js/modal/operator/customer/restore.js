/**
 * 顧客復元用のモーダル処理
 */

// 復元確認モーダルの表示
export function makeCustomerRestoreConfirmModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal, execMode } = elements;

    modalTitle.textContent = "顧客復元の確認";
    modalBody.textContent = "この顧客情報を復元してもよろしいですか？";
    execModal.textContent = "復元する";
    execModal.classList.remove("hidden");
    execModal.classList.remove("bg-red-500");
    execModal.classList.add("bg-green-500");
    cancelModal.textContent = "キャンセル";
    execMode.textContent = "restore";
    modal.classList.remove("hidden");
}

// 復元成功モーダルの表示
export function makeCustomerRestoreSuccessModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

    modalTitle.textContent = "復元完了";
    modalBody.textContent = "顧客情報の復元が完了しました。";
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");

    // 3秒後に画面をリロード
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

// 復元失敗モーダルの表示
export function makeCustomerRestoreFailModal(message = "顧客情報の復元に失敗しました。") {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

    modalTitle.textContent = "エラー";
    modalBody.textContent = message;
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");
}

/**
 * モーダル関連の要素を取得
 */
function getModalElements() {
    const elements = {
        modal: document.getElementById("modal"),
        modalTitle: document.getElementById("modalTitle"),
        modalBody: document.getElementById("modalBody"),
        execModal: document.getElementById("execModal"),
        cancelModal: document.getElementById("cancelModal"),
        execMode: document.getElementById("execMode")
    };

    if (Object.values(elements).some(element => !element)) {
        console.error("必要なモーダル要素が見つかりません");
        return null;
    }

    return elements;
} 