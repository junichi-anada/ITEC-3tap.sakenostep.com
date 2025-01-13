/**
 * 商品データインポートモーダルの表示
 */
export function makeItemImportModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

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

    // ファイル選択時の処理
    const fileInput = document.getElementById('importFile');
    const fileInfo = document.getElementById('fileInfo');
    const fileNameSpan = document.getElementById('fileName');

    fileInput?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileNameSpan.textContent = this.files[0].name;
            fileInfo.classList.remove('hidden');
        } else {
            fileInfo.classList.add('hidden');
        }
    });

    // モーダルを表示
    modal.classList.remove('hidden');
}

/**
 * インポート処理中モーダルの表示
 */
export function makeItemImportProcessingModal() {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

    modalTitle.textContent = "商品データをインポート中";
    execModal.classList.add('hidden');
    cancelModal.classList.add('hidden');

    const processingHtml = `
        <div class="flex flex-col items-center justify-center space-y-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            <p class="text-sm text-gray-500">商品データをインポートしています。しばらくお待ちください。</p>
        </div>
    `;

    modalBody.innerHTML = processingHtml;
    modal.classList.remove('hidden');
}

/**
 * インポート失敗モーダルの表示
 */
export function makeItemImportFailModal(errorMessage = '') {
    const elements = getModalElements();
    if (!elements) return;

    const { modal, modalTitle, modalBody, execModal, cancelModal } = elements;

    modalTitle.textContent = "インポート失敗";
    execModal.classList.add('hidden');

    const failHtml = `
        <div class="space-y-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">インポートに失敗しました</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>${errorMessage}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    modalBody.innerHTML = failHtml;
    modal.classList.remove('hidden');
} 