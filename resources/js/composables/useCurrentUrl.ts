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
 * `new URL()` needs a base to resolve a relative path, and VITE_APP_URL is it:
 * APP_URL, inlined by Vite at build time. It is required — without it a href
 * carrying a query or fragment keeps them, so `/links?page=2` never matches
 * `/links` and the nav item stops highlighting.
 *
 * Only `.pathname` is read and an absolute URL ignores the base, so the value
 * only has to be a valid origin, not the one actually being served.
 */
const toPathname = (url: string): string => {
    try {
        return new URL(url, import.meta.env.VITE_APP_URL).pathname;
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
