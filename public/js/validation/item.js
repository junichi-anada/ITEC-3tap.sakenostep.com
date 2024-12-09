/**
 * 商品情報の入力値チェック
 */
import { makeItemRegistFailModal } from "../modal/operator/item/regist.js";

export function validateItemForm() {
    const itemCode = document.getElementById("item_code").value;
    const name = document.getElementById("name").value;
    const categoryId = document.getElementById("category_id").value;
    const description = document.getElementById("description").value;
    const publishedAt = document.getElementById("published_at").value;
    const capacity = document.getElementById("capacity").value;
    const quantityPerUnit = document.getElementById("quantity_per_unit").value;

    let errorMessage = "";

    // 商品コードのチェック
    if (!itemCode || itemCode.trim() === "") {
        errorMessage += "商品コードは必須です。\n";
    } else if (!/^[A-Za-z0-9\-_]+$/.test(itemCode)) {
        errorMessage +=
            "商品コードは半角英数字、ハイフン、アンダースコアのみ使用できます。\n";
    }

    // 商品名のチェック
    if (!name || name.trim() === "") {
        errorMessage += "商品名は必須です。\n";
    }

    // カテゴリのチェック
    if (!categoryId || categoryId === "") {
        errorMessage += "カテゴリを選択してください。\n";
    }

    // 商品説明のチェック（任意だが入力された場合のみチェック）
    if (description && description.trim() !== "") {
        if (description.length > 1000) {
            errorMessage += "商品説明は1000文字以内で入力してください。\n";
        }
    }

    // 容量のチェック（任意だが入力された場合のみチェック）
    if (capacity && capacity.trim() !== "") {
        if (isNaN(capacity) || parseFloat(capacity) < 0) {
            errorMessage += "容量は0以上の数値で入力してください。\n";
        }
    }

    // ケース入数のチェック（任意だが入力された場合のみチェック）
    if (quantityPerUnit && quantityPerUnit.trim() !== "") {
        if (isNaN(quantityPerUnit) || parseInt(quantityPerUnit) < 0) {
            errorMessage += "ケース入数は0以上の整数で入力してください。\n";
        }
    }

    // 公開日時のチェック（任意だが入力された場合のみチェック）
    if (publishedAt && publishedAt.trim() !== "") {
        const publishDate = new Date(publishedAt);
        if (isNaN(publishDate.getTime())) {
            errorMessage += "公開日時の形式が正しくありません。\n";
        }
    }

    // エラーメッセージがある場合は、モーダルで表示
    if (errorMessage !== "") {
        // エラーメッセージの改行をHTMLの改行タグに変換
        const formattedMessage = errorMessage.replace(/\n/g, "<br>");
        makeItemRegistFailModal(formattedMessage);
        return false;
    }

    return true;
}
