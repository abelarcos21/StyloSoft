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
    base: '/StyloSoft/', // <- importante para subdirectorio
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],  // opcional: si quieres compilar CSS separado 'resources/css/app.css',
            refresh: true,
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
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});