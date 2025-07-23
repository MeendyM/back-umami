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
                "gray-100": "#F3F4F6",
                "gray-800": "#1F2937",
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
                
                // Colores adicionales para estados y botones
                "blue-100": "#DBEAFE",
                "blue-500": "#3B82F6",
                "blue-600": "#2563EB",
                "blue-800": "#1E40AF",
                
                "green-100": "#DCFCE7",
                "green-500": "#22C55E",
                "green-600": "#16A34A",
                "green-700": "#15803D",
                "green-800": "#166534",
                
                "yellow-100": "#FEF3C7",
                "yellow-500": "#EAB308",
                "yellow-600": "#CA8A04",
                "yellow-800": "#92400E",
                
                "orange-100": "#FFEDD5",
                "orange-500": "#F97316",
                "orange-600": "#EA580C",
                "orange-800": "#9A3412",
                
                "red-100": "#FEE2E2",
                "red-500": "#EF4444",
                "red-600": "#DC2626",
                "red-800": "#991B1B",
                
                "indigo-100": "#E0E7FF",
                "indigo-400": "#818CF8",
                "indigo-500": "#6366F1",
                "indigo-600": "#4F46E5",
                "indigo-800": "#3730A3",
                
                "amber-100": "#FEF3C7",
                "amber-500": "#F59E0B",
                "amber-600": "#D97706",
                "amber-800": "#92400E",
                
                "emerald-100": "#D1FAE5",
                "emerald-500": "#10B981",
                "emerald-600": "#059669",
                "emerald-800": "#065F46",
                
                "purple-100": "#F3E8FF",
                "purple-500": "#A855F7",
                "purple-600": "#9333EA",
                "purple-800": "#6B21A8",
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
