const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
    mode: "jit",
    content: [
        "./resources/Backend/js/**/*.blade.php",
        "./resources/Backend/js/**/*.vue",
        "./packages/bed-package-essentials/core/resources/Backend/js/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#2970FF",
                    dark: "#1E56CC",
                    darker: "#155EEF",
                    hover: "#1E56CC",
                    active: "#155EEF",
                    50: "#EFF4FF",
                    100: "#D1E0FF",
                    200: "#B2CCFF",
                    300: "#84ADFF",
                    400: "#528BFF",
                    500: "#2970FF",
                    600: "#1E56CC",
                    700: "#155EEF",
                    800: "#003194",
                    900: "#002266",
                },

                gray: {
                    ...defaultTheme.colors.gray,
                    800: "#1c1c1c",
                    900: "#111111",
                },
            },
            fontSize: {
                "2xs": ["0.55rem", { lineHeight: "0.75rem" }],
            },
        },
    },
    plugins: [],
};
