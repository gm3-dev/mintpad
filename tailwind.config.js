const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.vue",
        "./resources/js/**/*.js"
    ],
    safelist: [
        'addon-left', 'addon-right'
    ],
    darkMode: 'class',
    theme: {
        extend: {
            backgroundImage: {
                'waves': "url('/images/background-2.jpg')",
            },
            padding: {
                'full': '100%',
            },
            colors: {
                'primary': {
                    100: '#f8f9ff', // new
                    200: '#E0E7FF', // new
                    300: '#cce2fc',
                    400: '#eef2fd',
                    600: '#0077FF',
                    700: '#0265dd',
                },
                'mintpad': {
                    100: '#FAFAFA', // new
                    200: '#ECEDEF', // new
                    300: '#656f77',
                    400: '#8C98A9', // new
                    500: '#05121b',
                    600: '#000000',
                    700: '#2E384D', // new
                }
            },
            fontFamily: {
                sans: ['Graphik-Medium', ...defaultTheme.fontFamily.sans],
                bold: ['Graphik-Bold'],
                semibold: ['Graphik-Semibold'],
                medium: ['Graphik-Medium'],
                regular: ['Graphik-Regular'],
                jpegdev: ['JPEGDEVFONT-Black'],
                jpegdevmd: ['JPEGDEVFONT-Medium']
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
