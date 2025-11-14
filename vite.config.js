/* import { defineConfig } from 'vite'; //ANTES LOCAL
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
}); */

//PRODUCCION
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: '/StyloSoft/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build',  // ← Asegúrate que esté
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: '/StyloSoft/',
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
