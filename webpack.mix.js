const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
/*const webpack = require('webpack')

mix.webpackConfig ({
    plugins: [
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: false,
            __VUE_PROD_DEVTOOLS__: false,
        }),
    ],
})*/

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
// mix.webpackConfig({ resolve: { fallback: { fs: false, os: require.resolve("os-browserify/browser"), path: require.resolve("path-browserify") } } });
// mix.js('resources/js/app.js', 'public/js')
//     .vue()
//     .sass('resources/sass/app.scss', 'public/css')
//     .postCss('resources/css/app.css', 'public/css', [
//     require('postcss-import'),
//     require('tailwindcss'),
//     require('autoprefixer'),
// ]);
mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/mint.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })
    .version();
mix.copyDirectory('resources/fonts', 'public/fonts');
