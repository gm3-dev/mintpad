import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
const host = 'app.mintpad.test';

export default ({ mode }) => {
    process.env = Object.assign(process.env, loadEnv(mode, process.cwd(), ''));

    let server = null;
    if (process.env.APP_ENV == 'local') {
        server = {
            host,
            hmr: { host },
            https: {
                key: fs.readFileSync(`/Applications/MAMP/Library/OpenSSL/certs/${host}.key`),
                cert: fs.readFileSync(`/Applications/MAMP/Library/OpenSSL/certs/${host}.crt`),
            },
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
                    'resources/js/app.js'
                ],
                refresh: true,
            }),
        ],
        define: {
            'process.env': {}
        },
        resolve: {
            alias: {
                buffer: 'buffer/',
            }
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
}
