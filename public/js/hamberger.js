/**
 * ハンバーガーメニュー
 * bars -> ハンバーガーメニューのバー
 * xmark -> ハンバーガーメニュー閉じるアイコン
 * menuk -> 表示するメニュー本体
 *
 */
document.getElementById("hamburger").addEventListener("click", function () {
    const menu = document.getElementById("menu");
    const bars = document.getElementById("bars");
    const xmark = document.getElementById("xmark");

    if (menu.classList.contains("translate-x-full")) {
        menu.classList.remove("translate-x-full");
        menu.classList.add("translate-x-0");
        bars.classList.add("hidden");
        xmark.classList.remove("hidden");
    } else {
        menu.classList.add("translate-x-full");
        menu.classList.remove("translate-x-0");
        bars.classList.remove("hidden");
        xmark.classList.add("hidden");
    }
});
