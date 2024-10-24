import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

const localHost = 'localhost';
const prodHost = 'semjjonline.xyz';

export default ({ mode }) => {
    process.env = Object.assign(process.env, loadEnv(mode, process.cwd(), ''));

    let server = null;

    if (process.env.APP_ENV === 'local') {
        // Localhost SSL settings
        server = {
            host: localHost,
            hmr: { host: localHost },
            https: {
                key: fs.readFileSync('./app/ssl/localhost.key'),
                cert: fs.readFileSync('./app/ssl/localhost.crt'),
            },
        };
    } else if (process.env.APP_ENV === 'production') {
        // Ensure HTTPS in production
        server = {
            host: prodHost,
            hmr: { host: prodHost },
            https: true,
        };
    }

    return defineConfig({
        server: server,
        build: {
            sourcemap: true,
        },
        plugins: [
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            laravel({
                input: [
                    'resources/sass/app.scss',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
        ],
        define: {
            'process.env': {},
        },
        resolve: {
            alias: {
                buffer: 'buffer/',
            },
        },
        optimizeDeps: {
            esbuildOptions: {
                // Node.js global to browser globalThis
                define: {
                    global: 'globalThis',
                },
            },
        },
    });
};
