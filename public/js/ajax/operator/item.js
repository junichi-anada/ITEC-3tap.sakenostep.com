/**
 * オペレータ管理画面から商品情報を登録するためのAjax処理
 */

import {
    makeItemRegistConfirmModal,
    makeItemRegistSuccessModal,
    makeItemRegistFailModal,
} from "../../modal/operator/item/regist.js";
import {
    makeItemUpdateConfirmModal,
    makeItemUpdateSuccessModal,
    makeItemUpdateFailModal,
} from "../../modal/operator/item/update.js";
import {
    makeItemDeleteConfirmModal,
    makeItemDeleteSuccessModal,
    makeItemDeleteFailModal,
} from "../../modal/operator/item/delete.js";
import {
    makeItemImportModal,
    makeItemImportProcessingModal,
    makeItemImportFailModal,
    updateUploadProgress,
} from "../../modal/operator/item/import.js";
import { validateItemForm } from "../../validation/item.js";

document.addEventListener("DOMContentLoaded", function () {
    // 登録ボタンクリック → 登録確認モーダル表示
    const itemRegistButton = document.getElementById("item_regist");
    if (itemRegistButton) {
        itemRegistButton.addEventListener("click", function () {
            // バリデーションチェックを追加
            if (!validateItemForm()) {
                return;
            }
            makeItemRegistConfirmModal();
        });
    }

    // 更新ボタンクリック → 更新確認モーダル表示
    const itemUpdateButton = document.getElementById("item_update");
    if (itemUpdateButton) {
        itemUpdateButton.addEventListener("click", function () {
            // バリデーションチェックを追加
            if (!validateItemForm()) {
                return;
            }
            makeItemUpdateConfirmModal();
        });
    }

    // 削除ボタンクリック → 削除確認モーダル表示
    const itemDeleteButton = document.getElementById("item_delete");
    if (itemDeleteButton) {
        itemDeleteButton.addEventListener("click", function () {
            makeItemDeleteConfirmModal();
        });
    }

    // 商品データを読み込むボタンクリック → アップロードモーダル表示
    const itemUploadButton = document.getElementById("item_upload");
    if (itemUploadButton) {
        itemUploadButton.addEventListener("click", function () {
            makeItemImportModal();
        });
    }

    // 処理ボタンをクリックされた時の処理
    const itemExec = document.getElementById("execModal");
    if (itemExec) {
        itemExec.addEventListener("click", function () {
            if (document.getElementById("execMode").textContent == "regist") {
                registItem();
            }
            if (document.getElementById("execMode").textContent == "update") {
                updateItem();
            }
            if (document.getElementById("execMode").textContent == "delete") {
                deleteItem();
            }
            if (document.getElementById("execMode").textContent == "import") {
                uploadItem();
            }
        });
    }

    /**
     * registItem
     * フォームのデータをAJAXで送信
     * 送信先: /operator/item
     *
     * @return JSON
     */
    async function registItem() {
        const itemForm = document.getElementById("item_form");
        const formData = new FormData(itemForm);
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // formDataの_tokenを取得
        const csrfToken = formData.get("_token");

        try {
            // ajax送信
            const response = await fetch("/operator/item", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify(formDataObj),
            });

            const data = await response.json();

            if (response.status === 422) {
                // バリデーションエラーの場合
                let errorMessage = "";
                if (data.errors) {
                    Object.values(data.errors).forEach((error) => {
                        errorMessage += error[0] + "\n";
                    });
                }
                makeItemRegistFailModal(
                    errorMessage || "バリデーションエラーが発生しました。"
                );
                return;
            }

            if (data.message === "success") {
                makeItemRegistSuccessModal();
            } else {
                console.log(data.message);
                makeItemRegistFailModal(data.reason || "登録に失敗しました。");
            }
        } catch (error) {
            console.error("Error:", error);
            makeItemRegistFailModal("エラーが発生しました。");
        }
    }

    /**
     * deleteItem
     * 商品データを削除
     * 送信先: /operator/item/{id}
     *
     * @return JSON
     */
    async function deleteItem() {
        const itemForm = document.getElementById("item_form");
        const formData = new FormData(itemForm);
        const csrfToken = formData.get("_token");
        const itemId = document.getElementById("item_id").value;

        try {
            // ajax送信
            const response = await fetch(`/operator/item/${itemId}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.message === "success") {
                makeItemDeleteSuccessModal();
            } else {
                console.error("Delete failed:", data.message);
                makeItemDeleteFailModal(data.reason || "削除に失敗しました。");
            }
        } catch (error) {
            console.error("Error:", error);
            makeItemDeleteFailModal("エラーが発生しました。");
        }
    }

    /**
     * updateItem
     * 商品データを更新
     * 送信先: /operator/item/{id}
     *
     * @return JSON
     */
    async function updateItem() {
        const itemForm = document.getElementById("item_form");
        if (!itemForm) {
            console.error("Form not found");
            makeItemUpdateFailModal("フォームが見つかりません。");
            return;
        }

        const formData = new FormData(itemForm);
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // CSRFトークンをフォームから取得
        const csrfToken = formData.get("_token");
        if (!csrfToken) {
            console.error("CSRF token not found");
            makeItemUpdateFailModal("CSRFトークンが見つかりません。");
            return;
        }

        // URLからIDを取得
        const itemId = window.location.pathname.split("/").slice(-1)[0];
        const targetURL = "/operator/item/" + itemId;

        try {
            // ajax送信
            const response = await fetch(targetURL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    ...formDataObj,
                    _method: "PUT",
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (response.status === 422) {
                // バリデーションエラーの場合
                let errorMessage = "";
                if (data.errors) {
                    Object.values(data.errors).forEach((error) => {
                        errorMessage += error[0] + "\n";
                    });
                }
                makeItemUpdateFailModal(
                    errorMessage || "バリデーションエラーが発生しました。"
                );
                return;
            }

            if (data.message === "success") {
                makeItemUpdateSuccessModal();
            } else {
                console.log(data.message);
                makeItemUpdateFailModal(data.reason || "更新に失敗しました。");
            }
        } catch (error) {
            console.error("Error:", error);
            makeItemUpdateFailModal("エラーが発生しました。");
        }
    }

    /**
     * uploadItem
     * 商品データをアップロード
     * 送信先: /operator/item/import
     *
     * @return JSON
     */
    async function uploadItem() {
        const itemUploadForm = document.getElementById("importForm");
        const formData = new FormData(itemUploadForm);
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ||
            document.querySelector('input[name="_token"]')?.value;

        if (!formData.get("file") || formData.get("file").size === 0) {
            makeItemImportFailModal("ファイルが選択されていません。");
            return;
        }

        // アップロード中のモーダルを表示
        makeItemImportProcessingModal();

        try {
            // XMLHttpRequestを使用してアップロードの進捗を取得
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener("progress", (event) => {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    updateUploadProgress(percentComplete);
                }
            });

            // Promise化したXHRリクエスト
            const uploadPromise = new Promise((resolve, reject) => {
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            resolve(response);
                        } catch (e) {
                            reject(new Error("JSONのパースに失敗しました"));
                        }
                    } else {
                        reject(new Error(`HTTP error! status: ${xhr.status}`));
                    }
                };
                xhr.onerror = () =>
                    reject(new Error("ネットワークエラーが発生しました"));
            });

            // XHRの設定とリクエスト送信
            xhr.open("POST", "/operator/item/import");
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.send(formData);

            // アップロード完了を待機
            const data = await uploadPromise;

            if (data.success) {
                // インポート処理画面に遷移
                const taskCode = data.data?.taskCode;
                if (taskCode) {
                    window.location.href = `/operator/item/import/${taskCode}/progress`;
                } else {
                    makeItemImportFailModal("タスクコードが見つかりません。");
                }
            } else {
                console.error("Upload failed:", data.message);
                makeItemImportFailModal(
                    data.message || "アップロードに失敗しました"
                );
            }
        } catch (error) {
            console.error("Error:", error);
            makeItemImportFailModal(
                error.message || "アップロード中にエラーが発生しました"
            );
        }
    }
});
