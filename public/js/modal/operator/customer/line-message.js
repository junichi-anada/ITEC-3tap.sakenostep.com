/**
 * LINEメッセージ送信用のモーダル処理
 */

/**
 * 送信成功モーダルを表示
 */
export function makeLineMessageSuccessModal() {
    const modal = document.getElementById("modal");
    const modalContent = document.getElementById("modalContent");
    const modalTitle = document.getElementById("modalTitle");
    const modalBody = document.getElementById("modalBody");
    const modalFooter = document.getElementById("modalFooter");

    modalTitle.textContent = "送信完了";
    modalBody.textContent = "メッセージを送信しました。";

    // フッターボタンを作成
    modalFooter.innerHTML = `
        <button type="button" id="modalClose" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">
            閉じる
        </button>
    `;

    // モーダルを表示
    modal.classList.remove("hidden");
    modalContent.classList.remove("hidden");

    // 閉じるボタンのイベントリスナー
    const modalClose = document.getElementById("modalClose");
    modalClose.addEventListener("click", function () {
        modal.classList.add("hidden");
        modalContent.classList.add("hidden");
    });
}

/**
 * 送信失敗モーダルを表示
 * @param {string} message エラーメッセージ
 */
export function makeLineMessageFailModal(
    message = "メッセージの送信に失敗しました。"
) {
    const modal = document.getElementById("modal");
    const modalContent = document.getElementById("modalContent");
    const modalTitle = document.getElementById("modalTitle");
    const modalBody = document.getElementById("modalBody");
    const modalFooter = document.getElementById("modalFooter");

    modalTitle.textContent = "エラー";
    modalBody.textContent = message;

    // フッターボタンを作成
    modalFooter.innerHTML = `
        <button type="button" id="modalClose" class="bg-[#F4CF41] text-black px-8 py-1 rounded-md">
            閉じる
        </button>
    `;

    // モーダルを表示
    modal.classList.remove("hidden");
    modalContent.classList.remove("hidden");

    // 閉じるボタンのイベントリスナー
    const modalClose = document.getElementById("modalClose");
    modalClose.addEventListener("click", function () {
        modal.classList.add("hidden");
        modalContent.classList.add("hidden");
    });
}
