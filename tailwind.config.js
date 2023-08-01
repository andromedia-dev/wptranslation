const defaultTheme = require("tailwindcss/defaultTheme");
const colors = require("tailwindcss/colors");
const plugin = require("tailwindcss/plugin");

/** @type {import('tailwindcss').Config} */
module.exports = {
    important: ".wptranslation-app",
    content: ["./resources/js/**/*.*"],
    safelist: ["wptranslation-app"],
    theme: {
        extend: {
            fontFamily: {
                sans: ["InterVariable", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    // red
                    DEFAULT: "#ee4237",
                    50: "#fef3f2",
                    100: "#fee4e2",
                    200: "#fecdca",
                    300: "#fcaaa5",
                    400: "#f87971",
                    500: "#ee4237",
                    600: "#dc3126",
                    700: "#b9251c",
                    800: "#99231b",
                    900: "#7f231d",
                    950: "#450e0a",
                },
                gray: colors.gray,
            },
        },
    },
    plugins: [
        plugin(function ({ addUtilities, matchUtilities, theme }) {
            function extractColorVars(colorObj, colorGroup = "") {
                return Object.keys(colorObj).reduce((vars, colorKey) => {
                    const value = colorObj[colorKey];

                    const newVars = typeof value === "string" ? { [`--color${colorGroup}-${colorKey}`]: value } : extractColorVars(value, `-${colorKey}`);

                    return { ...vars, ...newVars };
                }, {});
            }

            addUtilities({
                "*": extractColorVars(theme("colors")),
            });
        }),
    ],
};
