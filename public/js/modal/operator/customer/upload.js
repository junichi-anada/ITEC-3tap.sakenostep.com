/**
 * 顧客更新確認モーダルの表示
 */

// 更新確認モーダルの表示
export function makeCustomerUploadConfirmModal() {
    const CustomerUplodeConfirmModal = document.getElementById("modal");
    if (CustomerUplodeConfirmModal) {
        // inputタグからCSRFトークンを取得
        const csrfTokenInput = document.querySelector('input[name="_token"]');
        const csrfToken = csrfTokenInput ? csrfTokenInput.value : "";

        if (!csrfToken) {
            console.error("CSRFトークンが見つかりません。");
            return;
        }

        // #modalの中にある#modaleTitleと#modalContentにテキストを追加
        document.getElementById("modalTitle").innerHTML =
            "顧客データファイルをアップロードしてください。";
        document.getElementById("modalContent").innerHTML = `
            <div class='py-4'>
            <form id="uploadForm" action="/upload" method="post" enctype="multipart/form-data" id='uploadForm'>
                <input type="hidden" name="_token" value="${csrfToken}">
                <div class="mb-4">
                    <input type="file" name="customerFile" id="customerFile" class="mt-1 block w-full" required>
                </div>
            </form>
            </div>
        `;
        document.getElementById("execModal").textContent = "アップロード";
        document.getElementById("cancelModal").textContent = "キャンセル";
        document.getElementById("execMode").textContent = "upload";
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
export function makeCustomerUploadSuccessModal() {
    const CustomerUploadCompleteModal = document.getElementById("modal");
    if (CustomerUploadCompleteModal) {
        document.getElementById("modalTitle").textContent = "更新完了";
        document.getElementById("modalContent").innerHTML =
            "顧客情報の読込が完了しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}

// 更新失敗モーダルの表示
export function makeCustomerUploadFailModal() {
    const CustomerUploadFailModal = document.getElementById("modal");
    if (CustomerUploadFailModal) {
        document.getElementById("modalTitle").textContent = "更新失敗";
        document.getElementById("modalContent").innerHTML =
            "顧客情報の読込に失敗しました。";
        document.getElementById("execModal").style.display = "none";
        document.getElementById("cancelModal").textContent = "閉じる";
        document.getElementById("modal").classList.remove("hidden");
    }
}
