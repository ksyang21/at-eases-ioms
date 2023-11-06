import './bootstrap';
import '../css/app.css';
import 'sweetalert2/dist/sweetalert2.min.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

/**
 * Font Awesome Icon
 */
import {library} from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons';

/**
 * Vue3 Datepicker
 */
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

/**
 * Sweetalert2
 */
import VueSweetalert2 from "vue-sweetalert2";

library.add(fas)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('font-awesome-icon', FontAwesomeIcon)
            .component('VueDatePicker', VueDatePicker)
            .use(ZiggyVue, Ziggy)
            .use(VueSweetalert2)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
