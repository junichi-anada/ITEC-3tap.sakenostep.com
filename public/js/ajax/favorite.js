/**
 * お気に入りに追加・削除するためのAjax処理
 */
document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = document.querySelector('input[name="_token"]').value;

    function handleResponse(response) {
        if (!response.ok) {
            return response.json().then((data) => {
                throw new Error(data.message || "エラーが発生しました。");
            });
        }
        return response.json().then((data) => {
            return data;
        });
    }

    /**
     * ボタンの表示/非表示を切り替える
     */
    function toggleButtonVisibility(button, showAddButton) {
        if (showAddButton) {
            button.classList.add("hidden");
            button.nextElementSibling.classList.remove("hidden");
        } else {
            button.classList.remove("hidden");
            button.nextElementSibling.classList.add("hidden");
        }
    }

    /**
     * リクエストを送信する
     */
    function sendRequest(url, method, body) {
        return fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            credentials: "include",
            body: JSON.stringify(body),
        }).then(handleResponse);
    }

    /**
     * お気に入りに追加する
     */
    document.querySelectorAll(".add-to-favorites").forEach((button) => {
        button.addEventListener("click", function () {
            const itemCode = this.getAttribute("data-item-code");

            sendRequest("/favorites/add", "POST", { item_code: itemCode })
                .then((data) => {
                    if (data.message === "お気に入りに追加しました") {
                        toggleButtonVisibility(this, true);
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => {
                    // console.error("エラー:", error);
                    alert(
                        "お気に入りの追加に失敗しました。\nサイト管理者までご連絡ください。"
                    );
                });
        });
    });

    /**
     * お気に入りから削除する
     */
    document.querySelectorAll(".del-to-favorites").forEach((button) => {
        button.addEventListener("click", function () {
            const itemCode = this.getAttribute("data-item-code");

            sendRequest(`/favorites/remove/${itemCode}`, "DELETE", {})
                .then((data) => {
                    if (data.message === "お気に入りから削除しました") {
                        if (!this.previousElementSibling) {
                            location.reload();
                        } else {
                            toggleButtonVisibility(
                                this.previousElementSibling,
                                false
                            );
                        }
                        this.classList.add("hidden");
                    } else {
                        console.log(data.message);
                    }
                })
                .catch((error) => {
                    // console.error("エラー:", error);
                    alert(
                        "お気に入りの削除に失敗しました。\nサイト管理者までご連絡ください"
                    );
                });
        });
    });
});
