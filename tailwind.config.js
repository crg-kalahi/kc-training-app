const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', 'sans-serif'],
                monotype: ['"Monotype Corsiva"', 'cursive'],
                poppins: ['Poppins', 'sans-serif'],
            },
        },
    },
    daisyui: {
        themes: ["cupcake", "dark", "valentine"],
    },

    plugins: [require('@tailwindcss/forms'), require("daisyui")],
};
