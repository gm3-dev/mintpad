const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');

// const webpack = require('webpack')
// if (mix.inProduction()) {
//     mix.webpackConfig ({
//         plugins: [
//             new webpack.DefinePlugin({
//                 __VUE_OPTIONS_API__: false,
//                 __VUE_PROD_DEVTOOLS__: false,
//             }),
//         ],
//     })
// }

mix.webpackConfig({
    plugins: [
        new NodePolyfillPlugin(),
    ]
})

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/mint.js', 'public/js')
    .js('resources/js/editor.js', 'public/js')
    .js('resources/js/guest.js', 'public/js')
    .js('resources/js/admin.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })
    .version();
mix.copyDirectory('resources/fonts', 'public/fonts');