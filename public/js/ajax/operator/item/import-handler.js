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
        this.modal = document.getElementById("modal");
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
        // アップロードボタンクリック時の処理
        this.execButton.addEventListener("click", async () => {
            // 二重送信防止
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
                `/operator/item/import/${taskCode}/status`,
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
                    if (typeof makeItemImportProcessingModal === "function") {
                        makeItemImportProcessingModal();
                    }
                    // 3秒後に再度ステータスを確認（処理状況を維持）
                    setTimeout(() => {
                        if (this.processingLock) {
                            this.checkImportStatus(taskCode);
                        }
                    }, 3000);
                } else if (
                    status === "completed" ||
                    status === "completed_with_errors"
                ) {
                    // 完了時は進捗確認画面へリダイレクト
                    window.location.href = `/operator/item/import/${taskCode}/progress`;
                } else if (status === "failed") {
                    if (typeof makeItemImportFailModal === "function") {
                        makeItemImportFailModal(
                            result.data.task.statusMessage ||
                                "インポート処理に失敗しました。"
                        );
                    }
                    this.processingLock = false;
                }
            } else {
                throw new Error(
                    result.message || "ステータスの取得に失敗しました。"
                );
            }
        } catch (error) {
            console.error("ステータス確認エラー:", error);
            if (typeof makeItemImportFailModal === "function") {
                makeItemImportFailModal(
                    error.message ||
                        "インポート状態の確認中にエラーが発生しました。"
                );
            }
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

        const formData = new FormData(this.form);
        if (!formData.get("file") || formData.get("file").size === 0) {
            if (typeof makeItemImportFailModal === "function") {
                makeItemImportFailModal("ファイルが選択されていません。");
            }
            return;
        }

        try {
            this.isProcessing = true;
            this.processingLock = true;
            console.log("インポート処理を開始します");
            this.form.dataset.processing = "true";
            this.execButton.disabled = true;

            // 処理中モーダルの表示
            if (typeof makeItemImportProcessingModal === "function") {
                makeItemImportProcessingModal();
            }

            const response = await fetch("/operator/item/import", {
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
            if (typeof makeItemImportFailModal === "function") {
                makeItemImportFailModal(
                    result.message || "インポート処理に失敗しました。"
                );
            }
            this.processingLock = false;
        } catch (error) {
            console.error("インポートエラーの詳細:", error);
            // エラーモーダルの表示
            if (typeof makeItemImportFailModal === "function") {
                makeItemImportFailModal(
                    error.message || "インポート処理中にエラーが発生しました。"
                );
            }
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
    new ItemImportHandler();
});
