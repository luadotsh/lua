import type { Page } from '@inertiajs/core';
import posthog from 'posthog-js';

import type { Auth } from './types';

const apiKey = import.meta.env.VITE_POSTHOG_API_KEY as string | undefined;
const host = import.meta.env.VITE_POSTHOG_HOST as string | undefined;
// Vite stringifies env vars, so explicit equality with 'true' avoids the
// gotcha where 'false' would be truthy. Mirrors the backend POSTHOG_ENABLED.
const enabled = import.meta.env.VITE_POSTHOG_ENABLED === 'true' && !!apiKey;

export const initializePostHog = (): void => {
    if (!enabled) {
        return;
    }

    posthog.init(apiKey as string, {
        api_host: host || 'https://us.i.posthog.com',
        ui_host: 'https://us.posthog.com',
        // Inertia never reloads the document, so autocapture would only ever
        // see the first page. capturePageview() is called per navigation.
        capture_pageview: false,
        capture_pageleave: true,
        cross_subdomain_cookie: true,
        session_recording: {
            maskAllInputs: true,
            maskTextSelector: '.ph-no-capture',
        },
    });
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
    if (!enabled) {
        return;
    }

    const auth = page.props.auth as Auth | undefined;

    if (!auth?.user) {
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
};

/**
 * Capture an Inertia-driven pageview. The SDK is configured with
 * `capture_pageview: false`, so this has to be invoked explicitly on every
 * navigation (and once at boot for the first page).
 */
export const capturePageview = (): void => {
    if (!enabled) {
        return;
    }

    posthog.capture('$pageview', { $current_url: window.location.href });
};

export default posthog;
