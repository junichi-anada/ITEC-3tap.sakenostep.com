/**
 * オペレータ管理画面から顧客情報を登録するためのAjax処理
 */

import {
    makeCustomerRegistConfirmModal,
    makeCustomerRegistSuccessModal,
    makeCustomerRegistFailModal,
} from "../../modal/operator/customer/regist.js";
import {
    makeCustomerUpdateConfirmModal,
    makeCustomerUpdateSuccessModal,
    makeCustomerUpdateFailModal,
} from "../../modal/operator/customer/update.js";
import {
    makeCustomerDeleteConfirmModal,
    makeCustomerDeleteSuccessModal,
    makeCustomerDeleteFailModal,
} from "../../modal/operator/customer/delete.js";
import {
    makeCustomerImportModal,
    makeCustomerImportProcessingModal,
    makeCustomerImportFailModal,
    updateUploadProgress,
} from "../../modal/operator/customer/import.js";
import {
    makeLineMessageSuccessModal,
    makeLineMessageFailModal,
} from "../../modal/operator/customer/line-message.js";
import { validateCustomerForm } from "../../validation/customer.js";
import {
    makeCustomerRestoreConfirmModal,
    makeCustomerRestoreSuccessModal,
    makeCustomerRestoreFailModal,
} from "../../modal/operator/customer/restore.js";

document.addEventListener("DOMContentLoaded", function () {
    // 登録ボタンクリック → 登録確認モーダル表示
    const customerRegistButton = document.getElementById("customer_regist");
    if (customerRegistButton) {
        customerRegistButton.addEventListener("click", function () {
            // バリデーションチェックを追加
            if (!validateCustomerForm()) {
                return;
            }
            makeCustomerRegistConfirmModal();
        });
    }

    // 更新ボタンクリック → 更新確認モーダル表示
    const customerUpdateButton = document.getElementById("customer_update");
    if (customerUpdateButton) {
        customerUpdateButton.addEventListener("click", function () {
            // バリデーションチェックを追加
            if (!validateCustomerForm()) {
                return;
            }
            makeCustomerUpdateConfirmModal();
        });
    }

    // 削除ボタンクリック → 削除確認モーダル表示
    const customerDeleteButton = document.getElementById("customer_delete");
    if (customerDeleteButton) {
        customerDeleteButton.addEventListener("click", function () {
            makeCustomerDeleteConfirmModal();
        });
    }

    // 顧客データを読み込むボタンクリック → アップロードモーダル表示
    const customerUploadButton = document.getElementById("customer_upload");
    if (customerUploadButton) {
        customerUploadButton.addEventListener("click", function () {
            makeCustomerImportModal();
        });
    }

    // 復元ボタンクリック → 復元確認モーダル表示
    const customerRestoreButton = document.getElementById("customer_restore");
    if (customerRestoreButton) {
        customerRestoreButton.addEventListener("click", function () {
            makeCustomerRestoreConfirmModal();
        });
    }

    // 処理ボタンをクリックされた時の処理
    const customerExec = document.getElementById("execModal");
    if (customerExec) {
        customerExec.addEventListener("click", function () {
            const execMode = document.getElementById("execMode").textContent;
            switch (execMode) {
                case "regist":
                    registCustomer();
                    break;
                case "update":
                    updateCustomer();
                    break;
                case "delete":
                    deleteCustomer();
                    break;
                case "import":
                    uploadCustomer();
                    break;
                case "restore":
                    restoreCustomer();
                    break;
            }
        });
    }

    /**
     * registCustomer
     * フォームのデータをAJAXで送信
     * 送信先: /customer/regist
     *
     * @return JSON
     */
    async function registCustomer() {
        const customerForm = document.getElementById("customer_form");
        const formData = new FormData(customerForm);
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // formDataの_tokenを取得
        const csrfToken = formData.get("_token");

        try {
            // ajax送信
            const response = await fetch("/operator/customer", {
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
                alert(errorMessage || "バリデーションエラーが発生しました。");
                return;
            }

            if (data.message === "success") {
                makeCustomerRegistSuccessModal(data.login_code, data.password);
            } else {
                console.log(data.message);
                makeCustomerRegistFailModal(data.reason);
            }
        } catch (error) {
            console.error("Error:", error);
            alert("エラーが発生しました。");
        }
    }

    /**
     * deleteCustomer
     * 顧客データを削除
     * 送信先: /customer/delete
     *
     * @return JSON
     */
    async function deleteCustomer() {
        const customerForm = document.getElementById("customer_form");
        const formData = new FormData(customerForm);
        const csrfToken = formData.get("_token");
        const userId = document.getElementById("user_id").value;

        try {
            // ajax送信
            const response = await fetch(`/operator/customer/${userId}`, {
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
                makeCustomerDeleteSuccessModal();
            } else {
                console.error("Delete failed:", data.message);
                makeCustomerDeleteFailModal();
            }
        } catch (error) {
            console.error("Error:", error);
            makeCustomerDeleteFailModal();
        }
    }

    /**
     * updateCustomer
     * 顧客データを更新
     * 送信先: /customer/update
     *
     * @return JSON
     */
    async function updateCustomer() {
        const customerForm = document.getElementById("customer_form");
        if (!customerForm) {
            console.error("Form not found");
            makeCustomerUpdateFailModal("フォームが見つかりません。");
            return;
        }

        const formData = new FormData(customerForm);
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // CSRFトークンをフォームから取得
        const csrfToken = formData.get("_token");
        if (!csrfToken) {
            console.error("CSRF token not found");
            makeCustomerUpdateFailModal("CSRFトークンが見つかりません。");
            return;
        }

        // URLからIDを取得
        const userId = window.location.pathname.split("/").slice(-1)[0];
        const targetURL = "/operator/customer/" + userId;

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
                makeCustomerUpdateFailModal(
                    errorMessage || "バリデーションエラーが発生しました。"
                );
                return;
            }

            if (data.message === "success") {
                makeCustomerUpdateSuccessModal();
            } else {
                console.log(data.message);
                makeCustomerUpdateFailModal(
                    data.reason || "更新に失敗しました。"
                );
            }
        } catch (error) {
            console.error("Error:", error);
            makeCustomerUpdateFailModal("エラーが発生しました。");
        }
    }

    /**
     * uploadCustomer
     * 顧客データをアップロード
     * 送信先: /customer/upload
     *
     * @return JSON
     */
    async function uploadCustomer() {
        const customerUploadForm = document.getElementById("importForm");
        const formData = new FormData(customerUploadForm);
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ||
            document.querySelector('input[name="_token"]')?.value;

        if (!formData.get("file") || formData.get("file").size === 0) {
            makeCustomerImportFailModal("ファイルが選択されていません。");
            return;
        }

        // アップロード中のモーダルを表示
        makeCustomerImportProcessingModal();

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
            xhr.open("POST", "/operator/customer/import");
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
                    window.location.href = `/operator/customer/import/${taskCode}/progress`;
                } else {
                    makeCustomerImportFailModal(
                        "タスクコードが見つかりません。"
                    );
                }
            } else {
                console.error("Upload failed:", data.message);
                makeCustomerImportFailModal(
                    data.message || "アップロードに失敗しました"
                );
            }
        } catch (error) {
            console.error("Error:", error);
            makeCustomerImportFailModal(
                error.message || "アップロード中にエラーが発生しました"
            );
        }
    }

    /**
     * restoreCustomer
     * 顧客データを復元
     * 送信先: /operator/customer/{customerCode}/restore
     *
     * @return void
     */
    async function restoreCustomer() {
        const customerForm = document.getElementById("customer_form");
        const formData = new FormData(customerForm);
        const csrfToken = formData.get("_token");
        const userId = document.getElementById("user_id").value;

        // デバッグ用のログ出力
        // console.log('Restore URL:', `/operator/customer/${userId}/restore`);
        // console.log('CSRF Token:', csrfToken);
        // console.log('User ID:', userId);

        try {
            // ajax送信
            const response = await fetch(`/operator/customer/${userId}/restore`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    // セッションクッキーを含める
                    credentials: 'same-origin'
                },
                // 空のJSONボディを送信
                body: JSON.stringify({})
            });

            // レスポンスの詳細をログ出力
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => null);
                console.error('Error data:', errorData);
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.message === "success") {
                makeCustomerRestoreSuccessModal();
            } else {
                console.error("Restore failed:", data.message);
                makeCustomerRestoreFailModal();
            }
        } catch (error) {
            console.error("Error:", error);
            makeCustomerRestoreFailModal();
        }
    }
});
