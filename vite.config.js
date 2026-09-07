import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/superadmin/theme.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost', // ganti dengan IP PC Anda
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});