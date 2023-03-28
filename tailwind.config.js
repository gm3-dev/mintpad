const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './resources/**/*.blade.php',
        "./resources/**/*.vue",
        "./resources/**/*.js"
    ],
    safelist: [
        'addon-left', 'addon-right'
    ],
    darkMode: 'class',
    theme: {
        extend: {
            backgroundImage: {
                'waves': "url('/images/background.jpg')",
                'waves-dark': "url('/images/background-dark.jpg')",
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
                    500: '#151E29',
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
                    800: '#151E29', // new
                    900: '#21272E', // new
                }
            },
            fontFamily: {
                sans: ['Graphik-Medium', ...defaultTheme.fontFamily.sans],
                bold: ['Graphik-Bold'],
                semibold: ['Graphik-Semibold'],
                medium: ['Graphik-Medium'],
                regular: ['Graphik-Regular'],
                jpegdev: ['JPEGDEVFONT-Black'],
                jpegdevmd: ['JPEGDEVFONT-Medium'],
                jpegdevbold: ['JPEGDEVFONT-Bold']
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
