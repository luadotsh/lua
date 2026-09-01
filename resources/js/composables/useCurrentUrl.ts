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
 * Reduce a href to the path used for comparison.
 *
 * Deliberately does not use `new URL()`. That needs a base to resolve relative
 * paths, and there is no honest one to give: `window.location` does not exist
 * on the server, and any hardcoded host would be a lie — links can point at a
 * customer's own domain. Only the path is ever compared, so the origin is
 * dropped whether it is present or not.
 *
 * The first replace strips `scheme://host` and the protocol-relative `//host`;
 * a single leading slash is a path and is left alone.
 */
const toPathname = (value: string): string => {
    const withoutOrigin = value.replace(
        /^([a-z][a-z0-9+.-]*:)?\/\/[^/?#]*/i,
        '',
    );
    const path = withoutOrigin.split('#')[0].split('?')[0];

    if (path === '') {
        return '/';
    }

    return path.startsWith('/') ? path : `/${path}`;
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
