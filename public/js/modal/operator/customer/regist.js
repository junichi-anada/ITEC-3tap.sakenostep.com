// モーダル関連の要素
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modalTitle");
const modalBody = document.getElementById("modalBody");
const execModal = document.getElementById("execModal");
const cancelModal = document.getElementById("cancelModal");
const execMode = document.getElementById("execMode");

/**
 * 顧客登録確認モーダルの表示
 */
export function makeCustomerRegistConfirmModal() {
    modalTitle.textContent = "登録確認";
    modalBody.textContent = "入力した顧客情報を登録してもよろしいですか？";
    execModal.textContent = "顧客情報を登録する";
    execModal.classList.remove("hidden");
    cancelModal.textContent = "キャンセル";
    execMode.textContent = "regist";
    modal.classList.remove("hidden");
}

/**
 * 顧客登録完了モーダルの表示
 */
export function makeCustomerRegistSuccessModal(loginCode, password) {
    modalTitle.textContent = "登録完了";
    modalBody.innerHTML = `顧客情報を登録しました。<br>ログインIDとパスワードをお伝えしてください。<br>ログインID：${loginCode}<br>パスワード：${password}`;
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    modal.classList.remove("hidden");

    // フォームをクリアして閉じる処理
    const clearFormAndCloseModal = function () {
        // フォームをクリア
        const form = document.getElementById("customer_form");
        if (form) {
            form.reset();
        }
        // モーダルを閉じる
        hideModal();
    };

    // 閉じるボタンのクリックイベント
    cancelModal.onclick = clearFormAndCloseModal;

    // モーダル背景のクリックイベント
    modal.onclick = function (e) {
        if (e.target === modal) {
            clearFormAndCloseModal();
        }
    };
}

/**
 * 顧客登録失敗モーダルの表示
 */
export function makeCustomerRegistFailModal(reason) {
    modalTitle.textContent = "登録失敗";
    modalBody.innerHTML = `顧客情報の登録に失敗しました。<br>理由：${reason}`;
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
