import type { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { toast } from 'vue-sonner';

import { favicon as faviconRoute } from '@/routes/websites';

export const cn = (...inputs: ClassValue[]) => {
    return twMerge(clsx(inputs));
};

export const toUrl = (href: NonNullable<InertiaLinkProps['href']>) => {
    return typeof href === 'string' ? href : href?.url;
};

export const formatNumber = (value: number): string => {
    return value.toLocaleString('en-US');
};

export const formatNumberCompact = (value: number | string): string => {
    // Some endpoints send counts already run through PHP's number_format(), e.g. "1,234".
    const numeric =
        typeof value === 'number' ? value : Number(value.replace(/,/g, ''));

    if (!Number.isFinite(numeric)) {
        return String(value);
    }

    return new Intl.NumberFormat('en-US', {
        notation: 'compact',
        compactDisplay: 'short',
        maximumFractionDigits: 1,
    }).format(numeric);
};

/**
 * Wayfinder builds paths, not URLs. That is right for navigating and for an
 * <img src>, but anything the user copies or hands to another system has to
 * resolve on its own.
 */
export const absoluteUrl = (path: string): string =>
    new URL(path, window.location.origin).toString();

export const copyToClipboard = async (
    text: string,
    message = 'Copied to clipboard',
) => {
    try {
        await navigator.clipboard.writeText(text);
        toast.success(message);
    } catch {
        toast.error('Failed to copy to clipboard');
    }
};

export const calcPercentage = (total: number, value: number): number => {
    if (total === 0) return 0;
    return Math.round((value / total) * 100);
};

export const calcTotal = (data: { y: number }[]): number => {
    if (!data || data.length === 0) return 0;
    return data.reduce((sum, item) => sum + (item.y ?? 0), 0);
};

export const kFormatter = (value: number): string => {
    if (value >= 1_000_000) return `${(value / 1_000_000).toFixed(1)}M`;
    if (value >= 1_000) return `${(value / 1_000).toFixed(1)}k`;
    return String(value);
};

/**
 * Through our own endpoint, which fetches the icon server-side.
 *
 * Pointing an <img> straight at Google's favicon service would hand them the
 * destination domain of every link in the list, along with the viewer's IP and
 * referrer — from the browser of everyone who opens the page. For a link
 * shortener, where the destination is the private part, that is the wrong
 * direction for the request to travel.
 */
export const favicon = (url: string): string => {
    if (!url) {
        return '';
    }

    return faviconRoute.url({ query: { url } });
};
