import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

const hostname = 'laraveldto.app.local';
const port     = 5173;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    server: {
        host: '0.0.0.0',
        port,
        strictPort: true,

        // *** DIT is cruciaal ***
        origin: `https://${hostname}:${port}`,   // <-- plugin gebruikt dit voor public/hot

        hmr:   { host: hostname },
        allowedHosts: ['.app.local'],
        cors: { origin: /https?:\/\/([\w-]+\.)?app\.local(?::\d+)?$/ },
    },
});
