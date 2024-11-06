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

    // 処理ボタンをクリックされた時の処理
    const customerExec = document.getElementById("execModal");
    if (customerExec) {
        customerExec.addEventListener("click", function () {
            // console.log(document.getElementById("execMode").textContent);
            if (document.getElementById("execMode").textContent == "regist") {
                registCustomer();
            }
            if (document.getElementById("execMode").textContent == "update") {
                console.log("更新処理");
            }
            if (document.getElementById("execMode").textContent == "delete") {
                deleteCustomer();
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
        const response = await fetch("/customer/regist", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify(formDataObj),
        });
        const data = await response.json();
        if (data.message === "success") {
            console.log(data.message);
            makeCustomerRegistSuccessModal();
        } else {
            console.log(data.message);
            makeCustomerRegistFailModal();
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

        // ajax送信
        const response = await fetch("/customer/delete", {
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
});
