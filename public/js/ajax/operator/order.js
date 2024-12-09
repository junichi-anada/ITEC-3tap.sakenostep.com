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
    makeOrderExportSuccessModal,
    makeOrderExportFailModal,
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

    // 処理ボタンをクリックされた時の処理
    const execModal = document.getElementById("execModal");
    if (execModal) {
        execModal.addEventListener("click", function () {
            const execMode = document.getElementById("execMode").textContent;
            if (execMode === "update") {
                updateOrder();
            }
            if (execMode === "export") {
                exportOrderCsv();
            }
        });
    }

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

    /**
     * 注文データをCSV出力
     */
    async function exportOrderCsv() {
        try {
            // 検索フォームのデータを取得
            const searchForm = document.getElementById("order_search_form");
            const formData = new FormData(searchForm);
            const searchParams = new URLSearchParams(formData);

            // CSRFトークンを取得
            const token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Ajax送信
            const response = await fetch(
                `/operator/order/export?${searchParams.toString()}`,
                {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": token,
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                }
            );

            // CSVファイルをダウンロード
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;

            // ファイル名を年月日_order.csvの形式に変更
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, "0");
            const day = String(today.getDate()).padStart(2, "0");
            a.download = `${year}${month}${day}_order.csv`;

            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            makeOrderExportSuccessModal();
        } catch (error) {
            console.error("Error:", error);
            makeOrderExportFailModal();
        }
    }
});
