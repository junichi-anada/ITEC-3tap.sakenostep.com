document.addEventListener("DOMContentLoaded", function () {
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
                        // DOMから削除するか、リストを更新する処理
                        // this.closest(".flex").remove(); // この行でクリックしたボタンの親要素を削除

                        // del-to-favoritesの入っているbuttonを非表示にする
                        this.style.display = "none";

                        // add-to-favoritesの入っている要素を表示する
                        this.previousElementSibling.style.display = "block";
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
