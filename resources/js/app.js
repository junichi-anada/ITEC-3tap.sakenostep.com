import "./bootstrap";

import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js"; // 日本語化のためにインポート
window.flatpickr = flatpickr;
flatpickr.localize(Japanese); // デフォルトを日本語に設定

document.addEventListener("DOMContentLoaded", function () {
    function initializeFlatpickr(element, fromInputId, toInputId) {
        if (!element) {
            console.error(`Element for Flatpickr not found. Details:`, {
                // element: element, // element is null/undefined here, so logging it directly isn't very helpful.
                // Logging the IDs is more useful for identifying which Flatpickr instance failed.
                fromInputId: fromInputId,
                toInputId: toInputId,
            });
            return;
        }
        flatpickr(element, {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ja", // 日本語指定
            onChange: function (selectedDates, dateStr, instance) {
                const fromEl = document.getElementById(fromInputId);
                const toEl = document.getElementById(toInputId);
                if (!fromEl || !toEl) return;

                if (selectedDates.length === 2) {
                    fromEl.value = instance.formatDate(
                        selectedDates[0],
                        "Y-m-d"
                    );
                    toEl.value = instance.formatDate(selectedDates[1], "Y-m-d");
                } else {
                    // 日付が2つ選択されていない場合 (0個または1個の場合) はクリアする
                    fromEl.value = "";
                    toEl.value = "";
                }
            },
            onReady: function (selectedDates, dateStr, instance) {
                const fromEl = document.getElementById(fromInputId);
                const toEl = document.getElementById(toInputId);
                if (!fromEl || !toEl) return;

                const fromDate = fromEl.value;
                const toDate = toEl.value;
                if (fromDate && toDate) {
                    instance.setDate([fromDate, toDate], false); // 第2引数 false で onChange をトリガーしない
                } else if (fromDate) {
                    instance.setDate([fromDate], false);
                }
            },
        });
    }

    const firstLoginDateRangeEl = document.querySelector(
        "#first_login_date_range"
    );
    const lastLoginDateRangeEl = document.querySelector(
        "#last_login_date_range"
    );
    const orderDateRangeEl = document.querySelector("#order_date_range");

    initializeFlatpickr(
        firstLoginDateRangeEl,
        "first_login_date_from",
        "first_login_date_to"
    );
    initializeFlatpickr(
        lastLoginDateRangeEl,
        "last_login_date_from",
        "last_login_date_to"
    );
    initializeFlatpickr(orderDateRangeEl, "order_date_from", "order_date_to");

    const publishedAtRangeEl = document.querySelector("#published_at_range");
    initializeFlatpickr(
        publishedAtRangeEl,
        "published_at_from",
        "published_at_to"
    );
});
