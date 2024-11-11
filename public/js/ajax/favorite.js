/**
 * お気に入りに追加・削除するためのAjax処理
 */
document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = document.querySelector('input[name="_token"]').value;

    function handleResponse(response) {
        return response.json().then((data) => {
            if (!response.ok) {
                throw new Error(data.message || "エラーが発生しました。");
            }
            return data;
        });
    }

    function toggleButtonVisibility(button, showAddButton) {
        if (showAddButton) {
            button.classList.add("hidden");
            button.nextElementSibling.classList.remove("hidden");
        } else {
            button.classList.remove("hidden");
            button.nextElementSibling.classList.add("hidden");
        }
    }

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
                    console.error("エラー:", error);
                    alert("お気に入りの追加に失敗しました。");
                });
        });
    });

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
                        alert(data.message);
                    }
                })
                .catch((error) => {
                    console.error("エラー:", error);
                    alert("お気に入りの削除に失敗しました。");
                });
        });
    });
});
