/** @type {import('tailwindcss').Config} */

const plugin = require("tailwindcss/plugin");
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                body: ["source-han-sans-japanese", "sans-serif"],
            },
        },
    },
    plugins: [
        function ({ addUtilities }) {
            addUtilities(
                {
                    ".hide-scrollbar": {
                        "-ms-overflow-style": "none", // IEとEdge
                        "scrollbar-width": "none", // Firefox
                    },
                    ".hide-scrollbar::-webkit-scrollbar": {
                        display: "none", // Chrome, Safari, Opera
                    },
                },
                ["responsive"]
            );
        },
    ],
};
