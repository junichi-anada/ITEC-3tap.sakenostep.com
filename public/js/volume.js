/**
 * 数量選択機能のJavaScript
 * プラスボタンとマイナスボタンをクリックした時に数量を増減する
 */
document.addEventListener("DOMContentLoaded", function () {
    // すべてのアイテムの＋と－ボタンにイベントリスナーを追加
    document.querySelectorAll(".flex.items-center").forEach(function (item) {
        const minusButton = item.querySelector("button:first-of-type"); // 「－」ボタン
        const plusButton = item.querySelector("button:last-of-type"); // 「＋」ボタン
        const inputField = item.querySelector('input[name="volume"]'); // 数値入力フィールド

        // 各ボタンと入力フィールドが存在する場合のみ処理を追加
        if (minusButton && plusButton && inputField) {
            // 「－」ボタンをクリックした時のイベント
            minusButton.addEventListener("click", function () {
                let currentValue = parseInt(inputField.value, 10); // 現在の値を取得して整数に変換
                if (currentValue > 1) {
                    // 値が1より大きい場合に減算
                    inputField.value = currentValue - 1; // 値を1減らす
                }
            });

            // 「＋」ボタンをクリックした時のイベント
            plusButton.addEventListener("click", function () {
                let currentValue = parseInt(inputField.value, 10); // 現在の値を取得して整数に変換
                inputField.value = currentValue + 1; // 値を1増やす
            });
        }
    });
});
