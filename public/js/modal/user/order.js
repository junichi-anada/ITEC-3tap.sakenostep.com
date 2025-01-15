/**
 * 注文確認モーダルの表示
 */

// 注文データ送信確認モーダルの表示
export function makeOrderConfirmModal() {
    const orderConfirmModal = document.getElementById("modal");
    if (orderConfirmModal) {
        document.getElementById("modalTitle").textContent = "注文確認";
        document.getElementById("modalContent").innerHTML =
            "注文リストの内容を送信します。<br>よろしいですか？";
        if (document.getElementById("execModal").style.display === "none") {
            document.getElementById("execModal").style.display = "block";
        }
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

/**
 * 注文データ送信完了モーダルの表示
 * @param {string} orderCode - 注文番号
 */
export function makeOrderSuccessModal(orderCode) {
    const orderSuccessModal = document.getElementById("modal");
    if (orderSuccessModal) {
        document.getElementById("modalTitle").textContent = "注文完了";
        document.getElementById("modalContent").innerHTML =
            `注文を送信しました。<br>
            注文番号：${orderCode}<br>
            ご注文ありがとうございました。`;
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";

        document.getElementById("modal").classList.remove("hidden");
    }

    // モーダルを閉じると同時に画面をリロード
    const cancelModal = document.getElementById("cancelModal");
    if (cancelModal) {
        cancelModal.addEventListener("click", function () {
            document.getElementById("modal").classList.add("hidden");
            // 画面リロード
            location.reload();
        });
    }
}

// 注文データ送信失敗モーダルの表示
export function makeOrderFailModal() {
    const orderFailModal = document.getElementById("modal");
    if (orderFailModal) {
        document.getElementById("modalTitle").textContent = "注文失敗";
        document.getElementById("modalContent").innerHTML =
            "注文の送信に失敗しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";

        document.getElementById("modal").classList.remove("hidden");
    }

    // モーダルを閉じると同時に画面をリロード
    const cancelModal = document.getElementById("cancelModal");
    if (cancelModal) {
        cancelModal.addEventListener("click", function () {
            document.getElementById("modal").classList.add("hidden");
            // 画面リロード
            location.reload();
        });
    }
}
