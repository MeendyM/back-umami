import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                white: "#fff",
                "dark-blue": "#1E1B4B", // indigo-950
                "dark-blue-border": "#312E81", // indigo-900
                "blue-hover": "#818CF8", // indigo-400
                "blue-bg-active": "#3730A3", // indigo-800
                gray: "#8994AB",
                "gray-500": "#8C97A7",
                "gray-700": "#2A2E33",
                "light-blue": "#E4ECFC",
                blue: "#4F46E5", // indigo-600
                "modal-bg-black": "#1A1F2B",
                "bgk-gray": "#FAFAFA",
                "color-suspension": "#FFEFEF",
                "tx-black": "#1A1F2B",
                "error-red": "#EB5757",
                "lay-off": "#FF6969",
                "bdr-img": "#C5CCDD",
                "properties-green": "#008D72",
                "users-blue": "#0774B2",
                "visits-purple": "#5F42B0",
                "licenses-yellow": "#EEAC03",
                "button-hover": "#6366F1", // indigo-500
            },
            fontSize: {
                smm: "14px",
                sm: "16px",
                xx: "30px",
                vx: "26px",
            },
            opacity: {
                60: ".60",
            },
        },
    },

    plugins: [forms, typography],
};
