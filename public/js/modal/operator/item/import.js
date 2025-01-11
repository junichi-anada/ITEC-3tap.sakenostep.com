/**
 * モーダル関連の要素を取得
 * @returns {Object|null} モーダル要素のオブジェクト、または要素が見つからない場合はnull
 */
function getModalElements() {
    const elements = {
        modal: document.getElementById("modal"),
        modalTitle: document.getElementById("modalTitle"),
        modalBody: document.getElementById("modalBody"),
        execModal: document.getElementById("execModal"),
        cancelModal: document.getElementById("cancelModal"),
        execMode: document.getElementById("execMode"),
    };

    // すべての要素が存在するか確認
    const missingElements = Object.entries(elements)
        .filter(([key, value]) => !value)
        .map(([key]) => key);

    if (missingElements.length > 0) {
        console.error(
            `以下のDOM要素が見つかりません: ${missingElements.join(", ")}`
        );
        return null;
    }

    return elements;
}

/**
 * 商品データインポートモーダルの表示
 */
export function makeItemImportModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal, execMode } =
        elements;

    modalTitle.textContent = "商品データのインポート";

    // ファイルアップロードフォームの作成
    const formHtml = `
        <div class="space-y-4">
            <p class="text-sm text-gray-500">
                Excel（xls形式, xlsx形式）、テキストファイル（csv形式、txt形式）のファイルをアップロードしてください。
            </p>
            <form id="importForm" class="space-y-4">
                <input type="hidden" name="_token" value="${
                    document.querySelector('input[name="_token"]')?.value || ""
                }">
                <div class="flex items-center justify-center w-full">
                    <label for="importFile" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-4 pb-4">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">クリックしてファイルを選択</span></p>
                            <p class="text-xs text-gray-500">XLS, XLSX, CSV, TXT (MAX. 10MB)</p>
                        </div>
                        <input id="importFile" type="file" class="hidden" accept=".xls,.xlsx,.csv,.txt" name="file" />
                    </label>
                </div>
                <div id="fileInfo" class="text-sm text-gray-500 hidden">
                    選択されたファイル: <span id="fileName"></span>
                </div>
            </form>
        </div>
    `;

    modalBody.innerHTML = formHtml;
    execModal.textContent = "アップロード";
    execModal.disabled = true;
    execModal.classList.remove("hidden");
    cancelModal.textContent = "キャンセル";
    cancelModal.classList.remove("hidden");
    execMode.textContent = "import";
    modal.classList.remove("hidden");
    modal.dataset.modalType = "import";

    // ファイル選択時の処理
    const importFile = document.getElementById("importFile");
    const fileInfo = document.getElementById("fileInfo");
    const fileName = document.getElementById("fileName");

    if (importFile && fileInfo && fileName) {
        importFile.addEventListener("change", function (e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileInfo.classList.remove("hidden");
                execModal.disabled = false;
            } else {
                fileInfo.classList.add("hidden");
                execModal.disabled = true;
            }
        });
    }

    // インポートハンドラーの初期化
    import("../../../ajax/operator/item/import-handler.js")
        .then((module) => {
            new module.ItemImportHandler();
        })
        .catch((error) => {
            console.error(
                "インポートハンドラーの読み込みに失敗しました:",
                error
            );
        });
}

/**
 * インポート処理中モーダルの表示
 */
export function makeItemImportProcessingModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

    modalTitle.textContent = "インポート処理中";
    modalBody.innerHTML = `
        <div class="text-center">
            <p class="mb-4">ファイルをアップロードしています。しばらくお待ちください。</p>
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span id="uploadProgressText" class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                            0%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                    <div id="uploadProgressBar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-300"></div>
                </div>
            </div>
        </div>
    `;

    execModal.classList.add("hidden");
    cancelModal.classList.add("hidden");
    modal.classList.remove("hidden");
    modal.dataset.modalType = "processing";
}

/**
 * インポート失敗モーダルの表示
 */
export function makeItemImportFailModal(message) {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

    modalTitle.textContent = "アップロード失敗";
    modalBody.innerHTML = message;
    execModal.classList.add("hidden");
    cancelModal.textContent = "閉じる";
    cancelModal.classList.remove("hidden");
    modal.classList.remove("hidden");
    modal.dataset.modalType = "fail";
}

/**
 * アップロードの進捗状況を更新
 * @param {number} progress - 進捗率（0-100）
 */
export function updateUploadProgress(progress) {
    const progressBar = document.getElementById("uploadProgressBar");
    const progressText = document.getElementById("uploadProgressText");

    if (progressBar && progressText) {
        const percentage = Math.round(progress);
        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `${percentage}%`;
    }
}

// モーダルを非表示
function hideModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal } = elements;
    modal.classList.add("hidden");
}

// DOMContentLoadedイベントで初期化
document.addEventListener("DOMContentLoaded", function () {
    const elements = getModalElements();
    if (elements) {
        elements.cancelModal.addEventListener("click", hideModal);
    }
});
