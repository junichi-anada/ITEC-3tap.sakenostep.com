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
            let csrfToken = "";
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            const inputToken = document.querySelector('input[name="_token"]');

            if (metaToken) {
                csrfToken = metaToken.getAttribute("content");
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
                        throw new Error("サーバーエラー: " + response.status);
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log("削除成功:", data.message);
                    // 成功したらページをリロード
                    location.reload();
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert(
                        "削除に失敗しました。ページをリロードして再試行してください。"
                    );
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

            // 注文リストから全商品のitem_codeとvolumeを収集
            const collectedItems = [];
            const itemContainers = document.querySelectorAll(
                "div.flex.flex-col.gap-y-4.border-b.pb-3"
            );

            itemContainers.forEach((container) => {
                const itemCodeButton =
                    container.querySelector(".del-to-order") ||
                    container.querySelector(".add-to-order");
                const inputField = container.querySelector(
                    'input[name="volume"].volume-input'
                );

                if (itemCodeButton && inputField) {
                    const itemCode =
                        itemCodeButton.getAttribute("data-item-code");
                    const volume = parseInt(inputField.value, 10);

                    if (itemCode && !isNaN(volume) && volume >= 1) {
                        collectedItems.push({
                            item_code: itemCode,
                            volume: volume,
                        });
                    } else {
                        console.warn(
                            `[execModal] Invalid data for item in container:`,
                            container,
                            `itemCode: ${itemCode}, volume: ${inputField.value}`
                        );
                    }
                } else {
                    console.warn(
                        "[execModal] Could not find item_code button or volume input in container:",
                        container
                    );
                }
            });

            console.log(
                "[execModal] Collected items for order:",
                collectedItems
            );

            if (collectedItems.length === 0) {
                // 注文するアイテムがない場合（既にカートが空になっているなど）
                // alert("注文する商品がありません。"); // 必要に応じてメッセージ表示
                makeOrderFailModal("注文する商品がありません。"); // 失敗モーダル表示
                return;
            }

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
                body: JSON.stringify({ items: collectedItems }), // 収集した商品情報を送信
            })
                .then((response) => {
                    if (!response.ok) {
                        // エラーレスポンスの場合は、レスポンスボディを読んでエラーメッセージを表示試行
                        return response
                            .json()
                            .then((errData) => {
                                throw {
                                    status: response.status,
                                    data: errData,
                                };
                            })
                            .catch(() => {
                                // JSONパース失敗時はステータスのみでエラーを投げる
                                throw {
                                    status: response.status,
                                    data: {
                                        message: `サーバーエラー (${response.status})`,
                                    },
                                };
                            });
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.order_code) {
                        // 注文完了モーダルを表示（注文番号を渡す）
                        makeOrderSuccessModal(data.order_code);
                    } else {
                        // サーバーから order_code が返ってこない場合もエラーとして扱う
                        console.error(
                            "[execModal] Order success but no order_code in response:",
                            data
                        );
                        makeOrderFailModal(
                            data.message ||
                                "注文処理中に不明なエラーが発生しました。"
                        );
                    }
                })
                .catch((error) => {
                    console.error(
                        "[execModal] Fetch error or server error:",
                        error
                    );
                    let errorMessage = "注文処理中にエラーが発生しました。";
                    if (error && error.data && error.data.message) {
                        errorMessage = error.data.message;
                    } else if (error && error.message) {
                        errorMessage = error.message;
                    }
                    makeOrderFailModal(errorMessage);
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
