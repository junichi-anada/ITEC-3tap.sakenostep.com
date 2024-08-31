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
});
