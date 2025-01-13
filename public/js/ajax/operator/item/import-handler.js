/**
 * 商品データインポート処理のハンドラー
 */
export class ItemImportHandler {
    /**
     * コンストラクタ
     */
    constructor() {
        this.form = document.getElementById("importForm");
        this.fileInput = document.getElementById("importFile");
        this.execButton = document.getElementById("execModal");
        this.isProcessing = false;
        this.processingLock = false;

        if (this.form && this.fileInput && this.execButton) {
            this.initialize();
        }
    }

    /**
     * 初期化
     */
    initialize() {
        this.execButton.addEventListener("click", async (e) => {
            e.preventDefault();
            if (this.isProcessing) return;
            await this.handleImport();
        });
    }

    /**
     * 処理状態の設定
     */
    setProcessingState(state) {
        this.isProcessing = state;
        this.processingLock = state;
    }

    /**
     * インポート処理
     */
    async handleImport() {
        if (this.isProcessing || this.processingLock) {
            console.log("処理中のため、リクエストをスキップします");
            return;
        }

        try {
            this.setProcessingState(true);
            makeItemImportProcessingModal();

            const formData = new FormData(this.form);
            const response = await fetch("/operator/item/import", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                },
                credentials: "same-origin"
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP error! status: ${response.status}`);
            }

            if (result.success && result.data?.taskCode) {
                // 進捗確認画面へリダイレクト
                window.location.href = `/operator/item/import/${result.data.taskCode}/progress`;
                return;
            }

            throw new Error(result.message || "インポート処理に失敗しました。");

        } catch (error) {
            console.error("インポートエラー:", error);
            makeItemImportFailModal(error.message || "インポート処理中にエラーが発生しました。");
        } finally {
            this.setProcessingState(false);
        }
    }
}

// DOMContentLoadedイベントでインスタンス化
document.addEventListener("DOMContentLoaded", () => {
    new ItemImportHandler();
});
