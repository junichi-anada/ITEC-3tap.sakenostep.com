document.addEventListener("DOMContentLoaded", function () {
    // "検索結果" タブが選択されている場合
    const searchTab = document.getElementById("search-tab");

    if (searchTab && searchTab.classList.contains("bg-white")) {
        // 親のスクロールコンテナを取得
        const scrollContainer = searchTab.closest(".overflow-x-scroll");

        // 親コンテナ内でタブをスクロールして表示
        if (scrollContainer) {
            // スクロールさせるために、ターゲットの位置を計算してスクロール
            const tabPosition =
                searchTab.offsetLeft - scrollContainer.offsetLeft;
            scrollContainer.scrollTo({
                left: tabPosition,
                behavior: "smooth",
            });
        }
    }
});
