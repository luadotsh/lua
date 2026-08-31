import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { App, DefineComponent } from 'vue';
import { createSSRApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { capturePageview, initializePostHog, syncPostHogContext } from './posthog';

// Third-party plugins still needed
import { i18nVue } from 'laravel-vue-i18n';

createInertiaApp({
    title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'Lua'}`,
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App: page, props, plugin }) {
        // On the server there is no element to mount into: the app is returned
        // for the renderer to turn into a string. createSSRApp is also what
        // lets the browser hydrate what the server sent rather than throw it
        // away and start over.
        const isServer = typeof window === 'undefined';

        if (!isServer) {
            // Identify + workspace group + first pageview. The same hooks fire
            // on every Inertia navigation below, so a workspace switch
            // re-attaches the right group and SPA navigations still register
            // as pageviews. None of it belongs on the server.
            initializePostHog();
            syncPostHogContext(props.initialPage);
            capturePageview();

            router.on('navigate', (event) => {
                syncPostHogContext(event.detail.page);
                capturePageview();
            });
        }

        // createSSRApp on the client too: that is what lets it hydrate the
        // markup the server sent rather than throw it away and start over.
        const app: App = createSSRApp({ render: () => h(page, props) })
            .use(plugin)
            .use(i18nVue, {
                resolve: async (lang: string) => {
                    const langs = import.meta.glob<{ default: Record<string, string> }>('../../lang/*.json');
                    return await langs[`../../lang/php_${lang}.json`]();
                },
            });

        if (isServer) {
            return app;
        }

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Reads localStorage and the media query, so it only means anything in a
// browser.
if (typeof window !== 'undefined') {
    initializeTheme();
}
