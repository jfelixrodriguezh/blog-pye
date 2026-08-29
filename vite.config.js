import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Bootstrap (compilado desde node_modules, ver resources/css/app.scss)
                'resources/css/app.scss',

                // Base + overrides de CORK (claro y oscuro)
                'resources/scss/light/assets/main.scss',
                'resources/scss/dark/assets/main.scss',

                // Chrome del layout horizontal-light-menu (sidebar, navbar, loader)
                'resources/scss/layouts/horizontal-light-menu/light/loader.scss',
                'resources/scss/layouts/horizontal-light-menu/light/structure.scss',
                'resources/scss/layouts/horizontal-light-menu/dark/loader.scss',
                'resources/scss/layouts/horizontal-light-menu/dark/structure.scss',

                // Perfect Scrollbar (estilos del plugin)
                'resources/scss/light/plugins/perfect-scrollbar/perfect-scrollbar.scss',

                // JS
                'resources/js/app.js',
                'resources/layouts/horizontal-light-menu/loader.js',
                'resources/layouts/horizontal-light-menu/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
