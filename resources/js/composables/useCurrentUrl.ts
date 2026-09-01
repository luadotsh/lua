import type { InertiaLinkProps } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import type { ComputedRef, DeepReadonly } from 'vue';
import { computed, readonly } from 'vue';

import { toUrl } from '@/lib/utils';

export type UseCurrentUrlReturn = {
    currentUrl: DeepReadonly<ComputedRef<string>>;
    isCurrentUrl: (
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrl?: string,
    ) => boolean;
    whenCurrentUrl: <T, F = null>(
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        ifTrue: T,
        ifFalse?: F,
    ) => T | F;
};

/**
 * The application's own origin, taken from APP_URL and inlined by Vite at
 * build time. `new URL()` needs a base to resolve a relative path, and this is
 * the configured one rather than an invented placeholder.
 *
 * Note this is fixed when the bundle is built, so an image built once and run
 * elsewhere carries the origin of whoever built it. That is fine here: only
 * `.pathname` is ever read and an absolute URL ignores the base entirely, so
 * the origin never reaches the comparison. In the browser the live origin is
 * preferred anyway.
 */
const configuredOrigin = (): string | undefined => {
    const configured = import.meta.env.VITE_APP_URL;

    if (typeof configured === 'string' && configured !== '') {
        return configured;
    }

    // Nothing configured: the browser knows its own origin, the server does not.
    return typeof window !== 'undefined' ? window.location.origin : undefined;
};

const toPathname = (url: string): string => {
    try {
        return new URL(url, configuredOrigin()).pathname;
    } catch {
        return url;
    }
};

export const useCurrentUrl = (): UseCurrentUrlReturn => {
    /**
     * Called per component rather than once at module scope. A module is
     * evaluated a single time in the SSR process and shared by every request
     * it serves, so a page object captured up there belongs to whichever
     * request happened to load the module first — and a computed built on it
     * caches that request's URL for all the others.
     */
    const page = usePage();

    // page.url is absent while Inertia is still resolving a visit, and a
    // missing value used to reach `.startsWith()` as undefined.
    const currentUrl = computed(() => toPathname(page.url ?? '/'));

    const isCurrentUrl = (
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        currentUrlOverride?: string,
    ) => {
        const current = currentUrlOverride ?? currentUrl.value;
        const pathname = toPathname(toUrl(urlToCheck));

        return (
            current === pathname ||
            (pathname !== '/' && current.startsWith(pathname + '/'))
        );
    };

    const whenCurrentUrl = <T, F = null>(
        urlToCheck: NonNullable<InertiaLinkProps['href']>,
        ifTrue: T,
        ifFalse?: F,
    ) => {
        return isCurrentUrl(urlToCheck) ? ifTrue : (ifFalse as F);
    };

    return {
        currentUrl: readonly(currentUrl),
        isCurrentUrl,
        whenCurrentUrl,
    };
};
