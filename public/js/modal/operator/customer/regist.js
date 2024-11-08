/**
 * 顧客登録確認モーダルの表示
 */
export function makeCustomerRegistConfirmModal() {
    const CustomerRegistConfirmModal = document.getElementById("modal");
    if (CustomerRegistConfirmModal) {
        document.getElementById("modalTitle").textContent = "登録確認";
        document.getElementById("modalContent").innerHTML =
            "入力した顧客情報を登録してもよろしいですか？";
        document.getElementById("execModal").style.display = "block";
        document.getElementById("execModal").textContent = "顧客情報を登録する";
        document.getElementById("cancelModal").textContent = "キャンセル";
        document.getElementById("execMode").textContent = "regist";
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

/**
 * 顧客登録完了モーダルの表示
 *
 * @return void
 */
export function makeCustomerRegistSuccessModal(loginCode, password) {
    const customerRegistSuccessModal = document.getElementById("modal");
    if (customerRegistSuccessModal) {
        document.getElementById("modalTitle").textContent = "登録完了";
        document.getElementById("modalContent").innerHTML =
            "顧客情報を登録しました。<br>ログインIDとパスワードをお伝えしてください。<br>ログインID：" +
            loginCode +
            "<br>パスワード：" +
            password;
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}

/**
 * 顧客登録失敗モーダルの表示
 *
 * @return void
 */
export function makeCustomerRegistFailModal(reason) {
    const CustomerRegistFailModal = document.getElementById("modal");
    if (CustomerRegistFailModal) {
        document.getElementById("modalTitle").textContent = "登録失敗";
        document.getElementById("modalContent").innerHTML =
            "顧客情報の登録に失敗しました。<br>理由：" + reason;
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}
