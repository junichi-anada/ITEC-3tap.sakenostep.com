/**
 * 顧客情報の入力値チェック
 */
import { makeCustomerRegistFailModal } from "../modal/operator/customer/regist.js";

export function validateCustomerForm() {
    const name = document.getElementById("name").value;
    const phone = document.getElementById("phone").value;
    const phone2 = document.getElementById("phone2").value;
    const fax = document.getElementById("fax").value;
    const postalCode = document.getElementById("postal_code").value;
    const address = document.getElementById("address").value;

    let errorMessage = "";

    // 顧客名のチェック
    if (!name || name.trim() === "") {
        errorMessage += "顧客名は必須です。\n";
    }

    // 電話番号のチェック
    if (!phone || phone.trim() === "") {
        errorMessage += "電話番号は必須です。\n";
    } else if (!/^[0-9\-]+$/.test(phone)) {
        errorMessage += "電話番号は数字とハイフンのみ使用できます。\n";
    }

    // 電話番号2のチェック（任意）
    if (phone2 && phone2.trim() !== "" && !/^[0-9\-]+$/.test(phone2)) {
        errorMessage += "電話番号2は数字とハイフンのみ使用できます。\n";
    }

    // FAX番号のチェック（任意）
    if (fax && fax.trim() !== "" && !/^[0-9\-]+$/.test(fax)) {
        errorMessage += "FAX番号は数字とハイフンのみ使用できます。\n";
    }

    // 郵便番号のチェック
    if (!postalCode || postalCode.trim() === "") {
        errorMessage += "郵便番号は必須です。\n";
    } else if (!/^\d{3}-?\d{4}$/.test(postalCode)) {
        errorMessage +=
            "郵便番号は正しい形式で入力してください（例：123-4567）。\n";
    }

    // 住所のチェック
    if (!address || address.trim() === "") {
        errorMessage += "住所は必須です。\n";
    }

    // エラーメッセージがある場合は、モーダルで表示
    if (errorMessage !== "") {
        // エラーメッセージの改行をHTMLの改行タグに変換
        const formattedMessage = errorMessage.replace(/\n/g, "<br>");
        makeCustomerRegistFailModal(formattedMessage);
        return false;
    }

    return true;
}
