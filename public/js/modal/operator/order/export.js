/**
 * 注文データCSV書出�ーダル関連の処理
 */

// モーダル関連の要素
const modal = document.getElementById("modal");
const modalTitle = document.getElementById("modalTitle");
const modalBody = document.getElementById("modalBody");
const execModal = document.getElementById("execModal");
const cancelModal = document.getElementById("cancelModal");
const execMode = document.getElementById("execMode");

/**
 * CSV書出確認モーダルの表示
 */
export function makeOrderExportConfirmModal() {
    modalTitle.textContent = "CSV書出の確認";
    modalBody.innerHTML = `
        <div class="text-center">
            <p class="mb-4">未出力の注文データをCSVファイルとして書き出します。</p>
            <p class="text-sm text-gray-500">※書き出し完了後、自動的にダウンロードが開始されます。</p>
        </div>
    `;
    execModal.textContent = "開始";
    execMode.textContent = "export";
    execModal.classList.remove("hidden");
    modal.classList.remove("hidden");
}

/**
 * CSV書出処理中モーダルの表示
 */
export function makeOrderExportProcessingModal() {
    modalTitle.textContent = "CSV書出中";
    modalBody.innerHTML = `
        <div class="flex flex-col items-center">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-[#F4CF41] border-t-transparent"></div>
            </div>
            <p class="mt-4 text-gray-600">CSVファイルを作成しています...</p>
        </div>
    `;
    execModal.classList.add("hidden");
    cancelModal.classList.add("hidden");
}

/**
 * CSV書出完了モーダルの表示
 */
export function makeOrderExportSuccessModal() {
    modalTitle.textContent = "CSV書出完了";
    modalBody.innerHTML = `
        <div class="text-center">
            <p class="text-green-600 mb-2">CSVファイルの作成が完了しました。</p>
            <p class="text-sm text-gray-500">ダウンロードが自動的に開始されます。</p>
        </div>
    `;
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    
    // 3秒後に画面をリロード
    setTimeout(() => {
        window.location.reload();
    }, 3000);
}

/**
 * CSV書出失敗モーダルの表示
 */
export function makeOrderExportFailModal(message = "CSV書出処理に失敗しました。") {
    modalTitle.textContent = "エラー";
    modalBody.innerHTML = `
        <div class="text-center">
            <p class="text-red-600">${message}</p>
        </div>
    `;
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
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
