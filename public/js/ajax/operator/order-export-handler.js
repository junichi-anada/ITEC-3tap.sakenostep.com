/**
 * 注文データCSV書出処理のハンドラー
 */
export class OrderExportHandler {
    constructor() {
        this.isProcessing = false;
        this.initialize();
    }

    /**
     * 初期化
     */
    initialize() {
        const exportButton = document.getElementById("export_csv");
        if (exportButton) {
            exportButton.addEventListener("click", () => {
                makeOrderExportConfirmModal();
            });
        }

        const execModal = document.getElementById("execModal");
        if (execModal) {
            execModal.addEventListener("click", async () => {
                if (document.getElementById("execMode").textContent === "export") {
                    await this.handleExport();
                }
            });
        }
    }

    /**
     * エクスポート処理
     */
    async handleExport() {
        if (this.isProcessing) return;

        try {
            this.isProcessing = true;
            makeOrderExportProcessingModal();

            // CSRFトークンを取得
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch('/operator/order/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // ファイル名を現在時刻から生成
            const now = new Date();
            const fileName = now.getFullYear() +
                           String(now.getMonth() + 1).padStart(2, '0') +
                           String(now.getDate()).padStart(2, '0') +
                           String(now.getHours()).padStart(2, '0') +
                           String(now.getMinutes()).padStart(2, '0') +
                           '.csv';
            
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            makeOrderExportSuccessModal();

        } catch (error) {
            console.error('Export error:', error);
            makeOrderExportFailModal();
        } finally {
            this.isProcessing = false;
        }
    }
} 