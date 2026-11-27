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
                    DEFAULT: "#005EA4",
                    dark: "#004D86",
                    darker: "#003967",
                    hover: "#004D86",
                    active: "#003967",
                    50: "#EDF6FF",
                    100: "#C9E0FF",
                    200: "#81BDFF",
                    300: "#479BFF",
                    400: "#0075E7",
                    500: "#005EA4",
                    600: "#004D86",
                    700: "#003967",
                    800: "#00284B",
                    900: "#001C36",
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
