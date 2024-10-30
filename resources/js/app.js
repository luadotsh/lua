import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Lua.sh';

// locales
import { i18nVue } from "laravel-vue-i18n";

// calendar
import VCalendar from "v-calendar";
import "v-calendar/style.css";

// Import modules...
import FloatingVue from "floating-vue";

// color picker
import Vue3ColorPicker from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(VCalendar)
            .use(i18nVue, {
                resolve: async (lang) => {
                    const langs = import.meta.glob("../../lang/*.json");
                    return await langs[`../../lang/php_${lang}.json`]();
                },
            })
            .use(Vue3ColorPicker)
            .use(FloatingVue)
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
