/**
 * 注文リストに追加・削除するためのAjax処理
 * - 注文リストに追加ボタンのクリックイベント
 * - 注文リストから削除ボタンのクリックイベント
 */
document.addEventListener("DOMContentLoaded", function () {
    // 注文リストに追加ボタンのクリックイベント
    document.querySelectorAll(".add-to-order").forEach((button) => {
        button.addEventListener("click", function () {
            // 必要なデータを取得
            const itemId = this.getAttribute("data-item-id");
            const siteId = this.getAttribute("data-site-id");

            // 対応する入力フィールドの値を取得
            const inputField = this.closest(".flex.gap-x-4").querySelector(
                'input[name="volume"]'
            );
            const volume = inputField ? inputField.value : 1; // 値がなければデフォルトで1

            // CSRFトークンの取得
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Ajaxリクエスト
            fetch("/order/add", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    item_id: itemId,
                    site_id: siteId,
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

    // 注文リストから削除ボタンのクリックイベント
    document.querySelectorAll(".del-to-order").forEach((button) => {
        button.addEventListener("click", function () {
            // 必要なデータを取得
            const detailCode = this.getAttribute("data-detail-code");

            // CSRFトークンの取得
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Ajaxリクエスト
            fetch(`/order/remove/${detailCode}`, {
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
});
