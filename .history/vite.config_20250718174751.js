import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/customer/theme.css',
                'resources/css/filament/admin/theme.css',
                resources/css/filament/manufacturer/theme.css],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});