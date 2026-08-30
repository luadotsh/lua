import { countryFlagUrl } from '@/lib/countries';

/**
 * A locale like `pt-BR` carries the country in its region subtag, which is
 * what the flag comes from. A bare `pt` has no country and gets no flag.
 */
export const languageCountryCode = (locale: string): string | null => {
    const region = locale.trim().split(/[-_]/)[1];

    return region && region.length === 2 ? region.toUpperCase() : null;
};

export const languageFlagUrl = (locale: string): string | null => {
    const code = languageCountryCode(locale);

    return code ? countryFlagUrl(code) : null;
};

const displayNames = (() => {
    try {
        return new Intl.DisplayNames(['en'], { type: 'language' });
    } catch {
        return null;
    }
})();

/**
 * The language alone — "Japanese", not "Japanese (Japan)". The region is
 * already on the flag beside it, so naming it twice only makes the column wide.
 * Falls back to the raw value when the runtime cannot name it.
 */
export const languageLabel = (locale: string): string => {
    const language = locale.trim().split(/[-_]/)[0];

    try {
        return displayNames?.of(language) ?? locale;
    } catch {
        return locale;
    }
};
