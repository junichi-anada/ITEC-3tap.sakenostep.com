/**
 * 顧客データインポート処理のハンドラー
 */
export class CustomerImportHandler {
    /**
     * コンストラクタ
     */
    constructor() {
        this.form = document.getElementById("importForm");
        this.fileInput = document.getElementById("importFile");
        this.execButton = document.getElementById("execModal");
        this.modal = document.getElementById("modal");
        this.isProcessing = false;
        this.processingLock = false; // 追加：処理ロック用フラグ

        if (this.form && this.fileInput && this.execButton) {
            this.initialize();
        }
    }

    /**
     * 初期化
     */
    initialize() {
        // アップロードボタンクリック時の処理
        this.execButton.addEventListener("click", async () => {
            // 二重送信防止の強化
            if (
                this.isProcessing ||
                this.processingLock ||
                this.form.dataset.processing === "true"
            ) {
                console.log("処理中のため、リクエストをスキップします");
                return;
            }
            await this.handleImport();
        });
    }

    /**
     * モーダルを非表示にする
     */
    hideModal() {
        if (this.modal) {
            this.modal.classList.add("hidden");
        }
    }

    /**
     * インポート処理の状態を確認
     * @param {string} taskCode - インポートタスクコード
     */
    async checkImportStatus(taskCode) {
        try {
            const response = await fetch(
                `/operator/customer/import/${taskCode}/status`,
                {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("ステータス確認結果:", result);

            if (result.success) {
                const status = result.data.task.status;
                console.log("タスクステータス:", status);

                // ステータスに応じたモーダル表示
                if (status === "pending" || status === "processing") {
                    if (
                        typeof makeCustomerImportProcessingModal === "function"
                    ) {
                        makeCustomerImportProcessingModal();
                    }
                    // 3秒後に再度ステータスを確認
                    setTimeout(() => this.checkImportStatus(taskCode), 3000);
                } else if (
                    status === "completed" ||
                    status === "completed_with_errors"
                ) {
                    // 完了時は進捗確認画面へリダイレクト
                    window.location.href = `/operator/customer/import/${taskCode}/progress`;
                } else if (status === "failed") {
                    if (typeof makeCustomerImportFailModal === "function") {
                        makeCustomerImportFailModal(
                            result.data.task.statusMessage ||
                                "インポート処理に失敗しました。"
                        );
                    }
                    // 処理完了後にロックを解除
                    this.processingLock = false;
                }
            } else {
                throw new Error(
                    result.message || "ステータスの取得に失敗しました。"
                );
            }
        } catch (error) {
            console.error("ステータス確認エラー:", error);
            if (typeof makeCustomerImportFailModal === "function") {
                makeCustomerImportFailModal(
                    error.message ||
                        "インポート状態の確認中にエラーが発生しました。"
                );
            }
            // エラー時にロックを解除
            this.processingLock = false;
        }
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
            this.isProcessing = true;
            this.processingLock = true; // 処理開始時にロック
            console.log("インポート処理を開始します");
            this.form.dataset.processing = "true";
            this.execButton.disabled = true;

            // 処理中モーダルの表示
            if (typeof makeCustomerImportProcessingModal === "function") {
                makeCustomerImportProcessingModal();
            }

            const formData = new FormData(this.form);
            console.log("リクエストを送信します");

            const response = await fetch("/operator/customer/import", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                credentials: "same-origin",
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            console.log("レスポンスを受信しました", response.status);

            const result = await response.json();
            console.log("レスポンス内容:", result);

            // 成功時の処理
            if (result.success && result.data?.taskCode) {
                console.log("インポート成功:", {
                    taskCode: result.data.taskCode,
                });

                // インポート状態の確認を開始
                await this.checkImportStatus(result.data.taskCode);
                return;
            }

            // エラー時の処理
            console.error("インポート処理に失敗しました", result);
            if (typeof makeCustomerImportFailModal === "function") {
                makeCustomerImportFailModal(
                    result.message || "インポート処理に失敗しました。"
                );
            }
            // エラー時にロックを解除
            this.processingLock = false;
        } catch (error) {
            console.error("インポートエラーの詳細:", error);
            // エラーモーダルの表示
            if (typeof makeCustomerImportFailModal === "function") {
                makeCustomerImportFailModal(
                    error.message || "インポート処理中にエラーが発生しました。"
                );
            }
            // エラー時にロックを解除
            this.processingLock = false;
        } finally {
            this.form.dataset.processing = "false";
            this.execButton.disabled = false;
            this.isProcessing = false;
        }
    }
}

// DOMContentLoadedイベントでインスタンス化
document.addEventListener("DOMContentLoaded", () => {
    new CustomerImportHandler();
});
