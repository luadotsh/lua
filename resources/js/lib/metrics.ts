// Every metric here is plottable over time, so every card in the header
// selects the series the chart draws.
export type MetricKey = 'events' | 'clicks' | 'qr_scans' | 'visitors';

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
};

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
