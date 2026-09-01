import type { Page } from '@inertiajs/core';
import type { PostHog } from 'posthog-js';

import type { Auth } from './types';

const apiKey = import.meta.env.VITE_POSTHOG_API_KEY as string | undefined;
const host = import.meta.env.VITE_POSTHOG_HOST as string | undefined;
// Vite stringifies env vars, so explicit equality with 'true' avoids the
// gotcha where 'false' would be truthy. Mirrors the backend POSTHOG_ENABLED.
const enabled = import.meta.env.VITE_POSTHOG_ENABLED === 'true' && !!apiKey;

/**
 * The SDK is loaded on demand, never imported at the top of this module.
 *
 * posthog-js does work as a side effect of being imported — among other
 * things it queues web-vitals collection through requestIdleCallback — so a
 * static import runs that code even when PostHog is switched off, and its
 * failures surface as console errors on a site that is not using it at all.
 * It also drags the whole SDK into the main bundle for every visitor.
 *
 * Only the type is imported statically, which erases at compile time.
 */
let loading: Promise<PostHog | null> | null = null;

const load = (): Promise<PostHog | null> => {
    if (!enabled) {
        return Promise.resolve(null);
    }

    // The promise is cached, not the resolved client. Caching the client left
    // a window between the first call and the import resolving, and every
    // caller that arrived inside it saw a null cache and started its own
    // init() — three of them do fire at boot, which PostHog answered with
    // "You have already initialized PostHog!" for each one after the first.
    loading ??= import('posthog-js').then(({ default: posthog }) => {
        posthog.init(apiKey as string, {
            api_host: host || 'https://us.i.posthog.com',
            ui_host: 'https://us.posthog.com',
            // Inertia never reloads the document, so autocapture would only
            // ever see the first page. capturePageview() is called per
            // navigation.
            capture_pageview: false,
            capture_pageleave: true,
            cross_subdomain_cookie: true,
            // PostHog fetches Chrome's web-vitals library at runtime and it
            // throws on load — "Cannot read properties of undefined (reading
            // 'startTime')" inside reportAllChanges, off a
            // requestIdleCallback. It arrives as an anonymous VM frame rather
            // than anything in our bundle, and only where PostHog is enabled.
            // Nothing here reads web vitals, so the collector is off rather
            // than throwing in every visitor's console. network_timing is a
            // separate flag, belongs to session replay, and stays on.
            capture_performance: {
                web_vitals: false,
            },
            session_recording: {
                maskAllInputs: true,
                maskTextSelector: '.ph-no-capture',
            },
        });

        return posthog;
    });

    return loading;
};

export const initializePostHog = (): void => {
    void load();
};

/**
 * Identify the current person and attach the workspace group, using what
 * Inertia already ships in shared props.
 *
 * Called at boot and on every navigation so a workspace switch re-attaches
 * the right group. Mirrors the backend app/Jobs/PostHog/SyncUser.php:
 * person -> User, group `workspace` -> the billing/plan parent.
 */
export const syncPostHogContext = (page: Page): void => {
    const auth = page.props.auth as Auth | undefined;

    if (!auth?.user) {
        return;
    }

    void load().then((posthog) => {
        if (!posthog) {
            return;
        }

        posthog.identify(auth.user.id, {
            $email: auth.user.email,
            $name: auth.user.name,
        });

        const workspace = auth.user.current_workspace;

        if (workspace) {
            posthog.group('workspace', workspace.id, {
                name: workspace.name,
                plan: workspace.plan?.name,
            });
        }
    });
};

/**
 * Capture an Inertia-driven pageview. The SDK is configured with
 * `capture_pageview: false`, so this has to be invoked explicitly on every
 * navigation (and once at boot for the first page).
 */
export const capturePageview = (): void => {
    const url = window.location.href;

    void load().then((posthog) => {
        posthog?.capture('$pageview', { $current_url: url });
    });
};
