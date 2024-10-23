import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

const host = '143.244.157.67';

export default ({ mode }) => {
    process.env = Object.assign(process.env, loadEnv(mode, process.cwd(), ''));

    let server = {
        host,
        hmr: { host },
    };

    // Optional: Check the environment and log a message if SSL is not required.
    if (process.env.APP_ENV == 'local') {
        console.log("Running in local environment, SSL is not required.");
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
