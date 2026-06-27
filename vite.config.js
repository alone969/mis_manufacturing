import path from 'path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
        react(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    server: {
        https: {
            key: 'C:\\Users\\Asus\\.config\\herd\\config\\valet\\Certificates\\mis_manufacturing.test.key',
            cert: 'C:\\Users\\Asus\\.config\\herd\\config\\valet\\Certificates\\mis_manufacturing.test.crt',
        },
        host: 'mis_manufacturing.test',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        hmr: {
            host: 'mis_manufacturing.test',
        },
    },
});
