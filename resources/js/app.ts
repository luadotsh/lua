import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

// Third-party plugins still needed
import { i18nVue } from 'laravel-vue-i18n';
import Vue3ColorPicker from 'vue3-colorpicker';
import 'vue3-colorpicker/style.css';

createInertiaApp({
    title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'Lua'}`,
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18nVue, {
                resolve: async (lang: string) => {
                    const langs = import.meta.glob<{ default: Record<string, string> }>('../../lang/*.json');
                    return await langs[`../../lang/php_${lang}.json`]();
                },
            })
            .use(Vue3ColorPicker)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
