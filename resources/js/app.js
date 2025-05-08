import "./bootstrap";

import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js"; // 日本語化のためにインポート
window.flatpickr = flatpickr;
flatpickr.localize(Japanese); // デフォルトを日本語に設定

document.addEventListener("DOMContentLoaded", function () {
    const firstLoginDateRangeEl = document.querySelector(
        "#first_login_date_range"
    );
    const lastLoginDateRangeEl = document.querySelector(
        "#last_login_date_range"
    );

    if (firstLoginDateRangeEl) {
        flatpickr(firstLoginDateRangeEl, {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ja", // 日本語指定
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById("first_login_date_from").value =
                        instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById("first_login_date_to").value =
                        instance.formatDate(selectedDates[1], "Y-m-d");
                } else if (selectedDates.length === 0) {
                    // 日付選択がクリアされた場合
                    document.getElementById("first_login_date_from").value = "";
                    document.getElementById("first_login_date_to").value = "";
                }
            },
            onReady: function (selectedDates, dateStr, instance) {
                const fromDate = document.getElementById(
                    "first_login_date_from"
                ).value;
                const toDate = document.getElementById(
                    "first_login_date_to"
                ).value;
                if (fromDate && toDate) {
                    instance.setDate([fromDate, toDate], false); // 第2引数 false で onChange をトリガーしない
                } else if (fromDate) {
                    instance.setDate([fromDate], false);
                }
            },
        });
    }

    if (lastLoginDateRangeEl) {
        flatpickr(lastLoginDateRangeEl, {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ja", // 日本語指定
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById("last_login_date_from").value =
                        instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById("last_login_date_to").value =
                        instance.formatDate(selectedDates[1], "Y-m-d");
                } else if (selectedDates.length === 0) {
                    document.getElementById("last_login_date_from").value = "";
                    document.getElementById("last_login_date_to").value = "";
                }
            },
            onReady: function (selectedDates, dateStr, instance) {
                const fromDate = document.getElementById(
                    "last_login_date_from"
                ).value;
                const toDate =
                    document.getElementById("last_login_date_to").value;
                if (fromDate && toDate) {
                    instance.setDate([fromDate, toDate], false);
                } else if (fromDate) {
                    instance.setDate([fromDate], false);
                }
            },
        });
    }
});
