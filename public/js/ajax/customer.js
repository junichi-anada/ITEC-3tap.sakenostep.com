/**
 * 顧客情報を登録するためのAjax処理
 */

import {
    makeCustomerRegistConfirmModal,
    makeCustomerRegistSuccessModal,
    makeCustomerRegistFailModal,
} from "../modal/operator/customer/regist.js";
import {
    makeCustomerUpdateConfirmModal,
    makeCustomerUpdateSuccessModal,
    makeCustomerUpdateFailModal,
} from "../modal/operator/customer/update.js";
import {
    makeCustomerDeleteConfirmModal,
    makeCustomerDeleteSuccessModal,
    makeCustomerDeleteFailModal,
} from "../modal/operator/customer/delete.js";
import {
    makeCustomerUploadConfirmModal,
    makeCustomerUploadSuccessModal,
    makeCustomerUploadFailModal,
} from "../modal/operator/customer/upload.js";

document.addEventListener("DOMContentLoaded", function () {
    // 登録ボタンクリック → 登録確認モーダル表示
    const customerRegistButton = document.getElementById("customer_regist");
    if (customerRegistButton) {
        customerRegistButton.addEventListener("click", function () {
            makeCustomerRegistConfirmModal();
        });
    }

    // 更新ボタンクリック → 更新確認モーダル表示
    const customerUpdateButton = document.getElementById("customer_update");
    if (customerUpdateButton) {
        customerUpdateButton.addEventListener("click", function () {
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
            makeCustomerUploadConfirmModal();
        });
    }

    // 処理ボタンをクリックされた時の処理
    const customerExec = document.getElementById("execModal");
    if (customerExec) {
        customerExec.addEventListener("click", function () {
            // console.log(document.getElementById("execMode").textContent);
            if (document.getElementById("execMode").textContent == "regist") {
                registCustomer();
            }
            if (document.getElementById("execMode").textContent == "update") {
                updateCustomer();
            }
            if (document.getElementById("execMode").textContent == "delete") {
                deleteCustomer();
            }
            if (document.getElementById("execMode").textContent == "upload") {
                uploadCustomer();
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
        if (data.message === "success") {
            makeCustomerRegistSuccessModal(data.login_code, data.password);
        } else {
            console.log(data.message);
            makeCustomerRegistFailModal(data.reason);
        }
        return response;
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

        // formDataのuser_codeを取得
        const formDataObj = {};
        formData.forEach((value, key) => {
            if (key == "user_code") {
                formDataObj[key] = value;
            }
        });

        // formDataの_tokenを取得
        const csrfToken = formData.get("_token");

        const targetURL = "/operator/customer/" + formDataObj["user_code"];

        // ajax送信
        const response = await fetch(targetURL, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify(formDataObj),
        });
        const data = await response.json();
        if (data.message === "success") {
            makeCustomerDeleteSuccessModal();
        } else {
            console.log(data.message);
            makeCustomerDeleteFailModal();
        }
        return response;
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

        const formData = new FormData(customerForm);

        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // formDataの_tokenを取得
        const csrfToken = formData.get("_token");

        const targetURL = "/operator/customer/" + formDataObj["user_code"];

        // ajax送信
        const response = await fetch(targetURL, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify(formDataObj),
        });
        const data = await response.json();
        if (data.message === "success") {
            makeCustomerUpdateSuccessModal();
        } else {
            console.log(data.message);
            makeCustomerUpdateFailModal();
        }
        return response;
    }

    /**
     * uploadCustomer
     * 顧客データをアップロード
     * 送信先: /customer/upload
     *
     * @return JSON
     */
    async function uploadCustomer() {
        const inProcessMessage = document.getElementById("processUpload");
        const customerUploadForm = document.getElementById("uploadForm");
        const formData = new FormData(customerUploadForm);

        if (
            !formData.get("customerFile") ||
            formData.get("customerFile").size === 0
        ) {
            alert("ファイルが選択されていません。");
            return;
        }

        inProcessMessage.style.display = "block";

        try {
            const response = await fetch("/operator/customer/upload", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": formData.get("_token"),
                },
                body: formData,
            });

            const data = await response.json();

            if (data.message === "success") {
                // 成功した場合、処理状況を表示するページにリダイレクト
                window.location.href =
                    "/operator/customer/upload/status?task_code=" +
                    encodeURIComponent(data.task_code);
            } else {
                console.log(data.message);
                makeCustomerUploadFailModal();
            }
        } catch (error) {
            makeCustomerUploadFailModal();
        }
    }
});
