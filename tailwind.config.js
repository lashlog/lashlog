// tailwind.config.js
export default {
    content: ["./resources/**/*.{blade.php,vue,js,ts}"],
    theme: {
        extend: {
            colors: {
                primary: "#D8B6A4", // ダスティピンク
                greige: "#CFC1B2", // グレージュ
                base: "#FFFFFF", // 白
            },
            fontFamily: {
                sans: ['"Instrument Sans"', "ui-sans-serif", "system-ui"],
            },
        },
    },
    plugins: [],
};
