document.addEventListener("DOMContentLoaded", function () {
    // マイリストに追加ボタンのクリックイベント
    document.querySelectorAll(".add-to-favorites").forEach((button) => {
        button.addEventListener("click", function () {
            // ボタンから item_id と site_id を取得
            const itemId = this.getAttribute("data-item-id");
            const siteId = this.getAttribute("data-site-id");

            // CSRFトークンの取得
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Ajaxリクエスト
            fetch("/favorites/add", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    item_id: itemId,
                    site_id: siteId,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.message) {
                        alert(data.message); // 成功メッセージの表示

                        // add-to-favoritesの入っているbuttonを非表示にする
                        this.style.display = "none";

                        // del-to-favoritesの入っている要素を表示する
                        this.nextElementSibling.style.display = "block";
                    } else {
                        alert("エラーが発生しました。");
                    }
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert("お気に入りの追加に失敗しました。");
                });
        });
    });

    // マイリストから削除ボタンのクリックイベント
    document.querySelectorAll(".del-to-favorites").forEach((button) => {
        button.addEventListener("click", function () {
            // ボタンから item_id と site_id を取得
            const itemId = this.getAttribute("data-item-id");
            const siteId = this.getAttribute("data-site-id");

            // CSRFトークンの取得
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Ajaxリクエスト
            fetch(`/favorites/remove/${itemId}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    site_id: siteId,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.message) {
                        alert(data.message); // 成功メッセージの表示

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
                            // 追加ボタンがない場合はマイリストをリロード
                            location.reload();
                        }

                        // del-to-favoritesの入っているbuttonを非表示にする
                        // this.style.display = "none";

                        // add-to-favoritesの入っている要素を表示する
                        // this.previousElementSibling.style.display = "block";
                    } else {
                        alert("エラーが発生しました。");
                    }
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert("お気に入りの削除に失敗しました。");
                });
        });
    });
});
