export type MetricKey =
    | 'events'
    | 'clicks'
    | 'qr_scans'
    | 'visitors'
    | 'links'
    | 'countries';

export type Metric = {
    value: number;
    previous: number;
    /** Null when the previous period had nothing to compare against. */
    change: number | null;
};

export type Overview = Record<MetricKey, Metric>;

export const metricLabels: Record<MetricKey, string> = {
    events: 'Events',
    clicks: 'Clicks',
    qr_scans: 'QR scans',
    visitors: 'Visitors',
    links: 'Links',
    countries: 'Countries',
};

/** Only these are plottable over time; the rest are period totals. */
export const plottableMetrics: MetricKey[] = [
    'events',
    'clicks',
    'qr_scans',
    'visitors',
];

const compact = new Intl.NumberFormat('en-US', {
    notation: 'compact',
    compactDisplay: 'short',
    maximumFractionDigits: 1,
});

const full = new Intl.NumberFormat('en-US');

export const formatCount = (value: number): string =>
    value >= 10_000 ? compact.format(value) : full.format(value);

export const formatChange = (change: number | null): string =>
    change === null ? '—' : `${Math.abs(change).toFixed(1)}%`;

export type Direction = 'up' | 'down' | 'flat';

export const directionOf = (change: number | null): Direction => {
    if (change === null || change === 0) {
        return 'flat';
    }

    return change > 0 ? 'up' : 'down';
};
