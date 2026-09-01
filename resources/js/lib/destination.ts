const PARAMETERS = [
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_term',
    'utm_content',
] as const;

export type UtmParameters = Partial<
    Record<(typeof PARAMETERS)[number], string | null>
>;

/**
 * Where a visitor actually lands, once the link's UTM parameters are put on the
 * destination. Mirrors `App\Actions\Link\BuildDestination`: a parameter the
 * destination URL already carries is left alone, and the link's UTMs fill in
 * the rest.
 *
 * Returns null when there is nothing to preview yet.
 */
export const buildDestination = (
    url: string,
    utms: UtmParameters,
): { base: string; added: string } | null => {
    if (!url.trim()) {
        return null;
    }

    let parsed: URL;

    try {
        parsed = new URL(url);
    } catch {
        // Still being typed. Nothing useful to show.
        return null;
    }

    const added = new URLSearchParams();

    for (const parameter of PARAMETERS) {
        const value = utms[parameter];

        if (value && !parsed.searchParams.has(parameter)) {
            added.append(parameter, value);
        }
    }

    const query = added.toString();

    return {
        base: parsed.toString(),
        added:
            query === ''
                ? ''
                : (parsed.search === '' ? '?' : '&') +
                  decodeURIComponent(query),
    };
};
