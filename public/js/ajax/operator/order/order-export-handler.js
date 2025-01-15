/**
 * 注文データCSV書出処理のハンドラー
 */
import {
    makeOrderExportProcessingModal,
    makeOrderExportSuccessModal,
    makeOrderExportFailModal,
} from "../../../modal/operator/order/export.js";

export class OrderExportHandler {
    /**
     * コンストラクタ
     */
    constructor() {
        this.form = document.getElementById("order_search_form");
        this.execButton = document.getElementById("execModal");
        this.isProcessing = false;
        this.processingLock = false;

        if (this.form && this.execButton) {
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
            await this.handleExport();
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
     * CSRFトークンを取得
     */
    getCsrfToken() {
        // meta要素からの取得を試みる
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (metaToken) return metaToken;

        // hidden入力要素からの取得を試みる
        const inputToken = document.querySelector('input[name="_token"]')?.value;
        if (inputToken) return inputToken;

        // どちらも取得できない場合はエラー
        throw new Error('CSRF token not found');
    }

    /**
     * エクスポート処理
     */
    async handleExport() {
        if (this.isProcessing || this.processingLock) {
            console.log("処理中のため、リクエストをスキップします");
            return;
        }

        try {
            this.setProcessingState(true);
            makeOrderExportProcessingModal();

            // CSRFトークンを取得
            const csrfToken = this.getCsrfToken();

            // OrderControllerのexportメソッドを呼び出し
            const response = await fetch('/operator/order/export', {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                },
                credentials: "same-origin"
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            // CSVファイルをダウンロード
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;

            // ファイル名を年月日時分_order.csvの形式に設定
            const now = new Date();
            const fileName = now.getFullYear() +
                           String(now.getMonth() + 1).padStart(2, '0') +
                           String(now.getDate()).padStart(2, '0') +
                           String(now.getHours()).padStart(2, '0') +
                           String(now.getMinutes()).padStart(2, '0') +
                           '_order.csv';
            
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            makeOrderExportSuccessModal();

        } catch (error) {
            console.error("エクスポートエラー:", error);
            makeOrderExportFailModal(error.message || "エクスポート処理中にエラーが発生しました。");
        } finally {
            this.setProcessingState(false);
        }
    }
} 