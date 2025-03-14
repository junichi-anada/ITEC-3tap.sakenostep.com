/**
 * 注文リストに追加・削除するためのAjax処理
 * - 注文リストに追加ボタンのクリックイベント
 * - 注文リストから削除ボタンのクリックイベント
 */
import {
    makeOrderConfirmModal,
    makeOrderSuccessModal,
    makeOrderFailModal,
} from "../modal/user/order.js";

document.addEventListener("DOMContentLoaded", function () {
    // 注文リストに追加ボタンのクリックイベント
    const addToOrderButtons = document.querySelectorAll(".add-to-order");
    if (addToOrderButtons) {
        addToOrderButtons.forEach((button) => {
            button.addEventListener("click", function () {
                // 必要なデータを取得
                const itemCode = this.getAttribute("data-item-code");

                // 対応する入力フィールドの値を取得
                const inputField = this.closest(".flex.flex-col").querySelector(
                    'input[name="volume"]'
                );
                const volume = inputField ? parseInt(inputField.value, 10) : 1; // 文字列を整数に変換

                //CSRFトークン取得
                const csrfToken = document.querySelector(
                    'input[name="_token"]'
                ).value;

                console.log(itemCode, volume);

                // Ajaxリクエスト
                fetch("/order/add", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        item_code: itemCode,
                        volume: volume,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.message) {
                            // alert(data.message); // 成功メッセージの表示
                            // ボタンの表示切り替え
                            this.classList.add("hidden"); // 追加ボタンを非表示
                            this.nextElementSibling.classList.remove("hidden"); // 削除ボタンを表示
                            // 削除ボタンのdata-detail-code属性に追加した注文リストの詳細コードを設定
                            this.nextElementSibling.setAttribute(
                                "data-detail-code",
                                data.detail_code
                            );
                        } else {
                            alert("エラーが発生しました。");
                        }
                    })
                    .catch((error) => {
                        console.error("エラー:", error);
                        alert("注文リストの追加に失敗しました。");
                    });
            });
        });
    }

    // 注文リストから削除ボタンのクリックイベント
    document.querySelectorAll(".del-to-order").forEach((button) => {
        button.addEventListener("click", function () {
            // 必要なデータを取得
            const itemlCode = this.getAttribute("data-item-code");

            //CSRFトークン取得
            const csrfToken = document.querySelector(
                'input[name="_token"]'
            ).value;

            // Ajaxリクエスト
            fetch(`/order/remove/${itemlCode}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                // body: JSON.stringify({
                //     site_id: siteId,
                // }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.message) {
                        // alert(data.message); // 成功メッセージの表示
                        // 追加ボタンがあった場合はボタンの切り替え
                        if (this.previousElementSibling) {
                            // ボタンの表示切り替え
                            this.classList.add("hidden"); // 削除ボタンを非表示
                            // 削除ボタンのdata-detail-code属性を削除
                            this.removeAttribute("data-detail-code");

                            this.previousElementSibling.classList.remove(
                                "hidden"
                            ); // 追加ボタンを表示
                        } else {
                            // 追加ボタンがない場合は注文リストをリロード
                            location.reload();
                        }
                    } else {
                        alert("エラーが発生しました。");
                    }
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert("注文リストの削除に失敗しました。");
                });
        });
    });

    // 注文リストの全削除ボタンのクリックイベント
    const delAllOrderButton = document.getElementById("del-all-order");
    if (delAllOrderButton) {
        delAllOrderButton.addEventListener("click", function () {
            // CSRFトークンを取得（メタタグまたはinput要素から）
            let csrfToken = '';
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            const inputToken = document.querySelector('input[name="_token"]');
            
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            } else if (inputToken) {
                csrfToken = inputToken.value;
            } else {
                console.error("CSRFトークンが見つかりません");
                alert("エラーが発生しました。ページをリロードしてください。");
                return;
            }

            // 確認ダイアログを表示
            if (!confirm("注文リストのアイテムをすべて削除しますか？")) {
                return;
            }

            fetch("/order/removeAll", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({}),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('サーバーエラー: ' + response.status);
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log('削除成功:', data.message);
                    // 成功したらページをリロード
                    location.reload();
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert("削除に失敗しました。ページをリロードして再試行してください。");
                    // エラー時もページをリロードする選択肢もある
                    // location.reload();
                });
        });
    }

    // 注文確認のモーダルを表示
    const openOrderModal = document.getElementById("openOrderModal");
    if (openOrderModal) {
        openOrderModal.addEventListener("click", function () {
            try {
                makeOrderConfirmModal();
                // #modalを表示
                document.getElementById("modal").classList.remove("hidden");
            } catch (error) {
                console.error("エラー:", error);
            }
        });
    }

    //注文処理実行
    const execModal = document.getElementById("execModal");
    if (execModal) {
        execModal.addEventListener("click", function () {
            // 注文確認モーダルを非表示にする。
            document.getElementById("modal").classList.add("hidden");

            //CSRFトークン取得
            const csrfToken = document.querySelector(
                'input[name="_token"]'
            ).value;

            fetch("/order/send", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    // 注文完了モーダルを表示（注文番号を渡す）
                    makeOrderSuccessModal(data.order_code);
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    makeOrderFailModal();
                });
        });
    }

    // 注文リストに反映ボタンのクリックイベント
    const addToAllOrder = document.getElementById("add-to-all-order");
    if (addToAllOrder) {
        addToAllOrder.addEventListener("click", function () {
            // CSRFトークン取得
            const csrfToken = document.querySelector(
                'input[name="_token"]'
            ).value;

            // order_codeを取得
            const orderCode = this.getAttribute("data-order-code");

            // Ajaxリクエスト
            fetch("/history/addAll", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    order_code: orderCode,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.message) {
                        // alert(data.message);
                        if (data.redirect) {
                            location.href = data.redirect;
                        }
                    } else {
                        alert("エラーが発生しました。");
                    }
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert("注文履歴のアイテム追加に失敗しました。");
                });
        });
    }
});
