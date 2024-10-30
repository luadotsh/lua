import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    safelist: [
        "text-red-500",
        "bg-red-100",
        "bg-red-50",
        "bg-red-500",
        "ring-red-300",

        "text-orange-500",
        "bg-orange-100",
        "bg-orange-50",
        "bg-orange-500",
        "ring-orange-300",

        "text-green-500",
        "bg-green-100",
        "bg-green-50",
        "bg-green-500",
        "ring-green-300",

        "text-cyan-500",
        "bg-cyan-100",
        "bg-cyan-50",
        "bg-cyan-500",
        "ring-cyan-300",

        "text-purple-500",
        "bg-purple-100",
        "bg-purple-50",
        "bg-purple-500",
        "ring-purple-300",

        "text-fuchsia-500",
        "bg-fuchsia-100",
        "bg-fuchsia-50",
        "bg-fuchsia-500",
        "ring-fuchsia-300",

        "text-teal-500",
        "bg-teal-100",
        "bg-teal-50",
        "bg-teal-500",
        "ring-teal-300",

        "text-blue-500",
        "bg-blue-100",
        "bg-blue-50",
        "bg-blue-500",
        "ring-blue-300",

        "text-indigo-500",
        "bg-indigo-100",
        "bg-indigo-50",
        "bg-indigo-500",
        "ring-indigo-300",

        "text-yellow-500",
        "bg-yellow-100",
        "bg-yellow-50",
        "bg-yellow-500",
        "ring-yellow-300",

        "text-zinc-500",
        "bg-zinc-100",
        "bg-zinc-50",
        "bg-zinc-500",
        "ring-zinc-300",

        "text-pink-500",
        "bg-pink-100",
        "bg-pink-50",
        "bg-pink-500",
        "ring-pink-300",
    ],

    theme: {
        extend: {
            backgroundImage: {
                "lua-gradient":
                    "linear-gradient(102.01deg, #FCF0BE 8.77%, #EC9EC3 46.4%, #6F388D 98.37%)",
            },
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require("@tailwindcss/aspect-ratio"),
    ],
};
