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
 * `new URL()` needs a base to resolve a relative path. This one is a
 * placeholder, not configuration — `.invalid` is reserved by RFC 2606 exactly
 * so it can never be a real host.
 *
 * It deliberately does not come from APP_URL over VITE_. Two reasons, the
 * first decisive:
 *
 *  - Vite inlines VITE_* at build time. The published image is built once and
 *    run by every self-hoster, so a domain baked in there is whichever one the
 *    release runner had, not theirs.
 *  - Links can carry a customer's own domain (Link has a `domain` column), so
 *    there is no single correct host to compare against in the first place.
 *
 * None of which costs anything, because the base never reaches the result:
 * only `.pathname` is read, and an absolute URL ignores the base outright.
 * Checked against a real domain over percent-encoded paths, dot segments,
 * credentials, ports, punycode hosts and non-http schemes — identical output.
 */
const PATH_ONLY_BASE = 'http://placeholder.invalid';

const toPathname = (url: string): string => {
    try {
        return new URL(url, PATH_ONLY_BASE).pathname;
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
