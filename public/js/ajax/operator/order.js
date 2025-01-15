/**
 * オペレータ管理画面から注文情報を管理するためのAjax処理
 */

import {
    makeOrderUpdateConfirmModal,
    makeOrderUpdateSuccessModal,
    makeOrderUpdateFailModal,
} from "../../modal/operator/order/update.js";

import {
    makeOrderExportConfirmModal,
} from "../../modal/operator/order/export.js";

document.addEventListener("DOMContentLoaded", function () {
    // 更新ボタンクリック → 更新確認モーダル表示
    const updateQuantitiesButton = document.getElementById("update_quantities");
    if (updateQuantitiesButton) {
        updateQuantitiesButton.addEventListener("click", function () {
            makeOrderUpdateConfirmModal();
        });
    }

    // CSV書出ボタンクリック → CSV書出確認モーダル表示
    const exportCsvButton = document.getElementById("export_csv");
    if (exportCsvButton) {
        exportCsvButton.addEventListener("click", function () {
            makeOrderExportConfirmModal();
        });
    }

    // 実行ボタンのイベントリスナー
    const execModal = document.getElementById("execModal");
    if (execModal) {
        execModal.addEventListener("click", async () => {
            const execMode = document.getElementById("execMode");
            if (execMode && execMode.textContent === "export") {
                if (window.orderExportHandler) {
                    await window.orderExportHandler.handleExport();
                }
            }
        });
    }

    // エクスポートハンドラーの初期化（即時実行）
    import("./order/order-export-handler.js")
        .then((module) => {
            window.orderExportHandler = new module.OrderExportHandler();
        })
        .catch((error) => {
            console.error("エクスポートハンドラーの読み込みに失敗しました:", error);
        });

    /**
     * 注文情報を更新
     */
    async function updateOrder() {
        try {
            // フォームデータを取得
            const form = document.getElementById("order_detail_form");
            const formData = new FormData(form);

            // FormDataをオブジェクトに変換
            const orderData = {
                order_code: formData.get("order_code"),
                details: [],
            };

            // 注文明細データを収集
            const details = formData
                .getAll("details[][item_code]")
                .map((itemCode, index) => ({
                    item_code: itemCode,
                    quantity: formData.getAll("details[][quantity]")[index],
                }));
            orderData.details = details;

            // CSRFトークンを取得
            const token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Ajax送信
            const response = await fetch("/operator/order/update", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: JSON.stringify(orderData),
            });

            const data = await response.json();

            if (data.success) {
                makeOrderUpdateSuccessModal();
            } else {
                makeOrderUpdateFailModal(data.message);
            }
        } catch (error) {
            console.error("Error:", error);
            makeOrderUpdateFailModal();
        }
    }
});
