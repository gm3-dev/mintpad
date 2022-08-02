const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'primary': {
                    100: '#f9fcff',
                    200: '#f0f4fe',
                    300: '#cce2fc',
                    400: '#0071f9',
                    600: '#0071f9',
                    700: '#0265dd',
                },
                'mintpad': {
                    100: '#e8e8e8',
                    200: '#e1e5e8',
                    300: '#656f77',
                    400: '#3b444c',
                    500: '#05121b',
                    600: '#000000',
                }
            },
            fontFamily: {
                sans: ['Graphik-Medium', ...defaultTheme.fontFamily.sans],
                semibold: ['Graphik-Semibold'],
                medium: ['Graphik-Medium'],
                regular: ['Graphik-Regular'],
                jpegdev: ['JPEGDEVFONTBlack'],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
