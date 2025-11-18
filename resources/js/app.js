import '../css/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'admin-lte/dist/css/adminlte.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

import $ from 'jquery';
window.$ = window.jQuery = $;

// Importar Select2 y pasarle jQuery explícitamente
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

select2($); // Esto registra Select2 en jQuery

import 'bootstrap';
import 'admin-lte/dist/js/adminlte.min.js';

// Vue + Inertia
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'StyloSoft';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: { color: '#4B5563' },
});
