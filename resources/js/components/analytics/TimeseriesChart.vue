<script setup lang="ts">
import { VisArea, VisAxis, VisGroupedBar, VisLine, VisXYContainer } from '@unovis/vue';
import { useId } from 'reka-ui';
import { computed, ref } from 'vue';
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    type ChartConfig,
} from '@/components/ui/chart';
import dayjs from '@/dayjs';
import { formatCount, metricLabels, type MetricKey } from '@/lib/metrics';
import { cn } from '@/lib/utils';

export type TimeseriesPoint = {
    bucket: string;
    events: number;
    clicks: number;
    qr_scans: number;
    visitors: number;
};

const props = defineProps<{
    series: TimeseriesPoint[];
    metric: MetricKey;
    group: string;
}>();

const CROSSHAIR_CIRCLE_RADIUS = 4;

/** A literal, not `var(--color-value)`: the bar fill does not resolve the var. */
const SERIES_COLOR = '#8b5cf6';

const SHAPES = [
    { value: 'line', label: 'Line' },
    { value: 'bar', label: 'Bar' },
] as const;

type Shape = (typeof SHAPES)[number]['value'];

const shape = ref<Shape>('line');

const chartConfig = computed<ChartConfig>(() => ({
    value: { label: metricLabels[props.metric], color: SERIES_COLOR },
}));

const chartData = computed(() =>
    props.series.map((point, index) => ({
        index,
        bucket: point.bucket,
        value: Number(point[props.metric as keyof TimeseriesPoint] ?? 0),
    })),
);

const hasData = computed(() => chartData.value.some((point) => point.value > 0));

const xAccessor = (d: { index: number }) => d.index;
const yAccessors = [(d: { value: number }) => d.value];

const lineColors = [SERIES_COLOR];

// A unique id per instance, so two charts on one page never collide on the
// <defs> they inject.
const gradientId = useId();
const valueGradientId = computed(() => `value-fill-${gradientId}`);
const areaColors = computed(() => [`url(#${valueGradientId.value})`]);

// A soft vertical fade from the series colour down to almost transparent.
const svgDefs = computed(
    () => `
    <linearGradient id="${valueGradientId.value}" x1="0" y1="0" x2="0" y2="1">
        <stop offset="5%" stop-color="${SERIES_COLOR}" stop-opacity="0.28" />
        <stop offset="95%" stop-color="${SERIES_COLOR}" stop-opacity="0.02" />
    </linearGradient>
`,
);

// At most six labels on the x axis, however many points are plotted.
const xNumTicks = computed(() => Math.min(6, chartData.value.length || 1));

/**
 * Pinned to the data. Left to itself the container derives the y extent from
 * every component in it, and the area and the line each contributing the same
 * series stacked the domain to twice the total — which flattened the line onto
 * the axis while the ticks still read 0–39.
 */
const yDomain = computed<[number, number]>(() => {
    const max = Math.max(...chartData.value.map((point) => point.value), 0);

    return [0, max === 0 ? 1 : max];
});

// Hours and minutes need the time of day; days and months do not.
const bucketFormat = computed(() =>
    props.group === 'minute' || props.group === 'hour'
        ? 'MMM D, HH:mm'
        : 'MMM D',
);

const xTickFormat = (tick: number): string => {
    const point = chartData.value[Math.round(tick)];

    return point ? dayjs(point.bucket).format(bucketFormat.value) : '';
};

const tooltipTemplate = (d: { bucket: string; value: number }): string =>
    `<div class="rounded-md border border-border bg-popover px-3 py-2 text-xs text-popover-foreground shadow-md">
        <div class="mb-1 text-muted-foreground">${dayjs(d.bucket).format(bucketFormat.value)}</div>
        <div class="font-medium tabular-nums">${formatCount(d.value)} ${metricLabels[props.metric].toLowerCase()}</div>
    </div>`;
</script>

<template>
    <div class="rounded-lg border border-border bg-card p-4">
        <div class="mb-2 flex items-center justify-between gap-3">
            <h2 class="text-sm font-medium text-foreground">
                {{ metricLabels[metric] }} over time
            </h2>

            <div
                v-if="hasData"
                class="flex gap-0.5 rounded-md border border-border p-0.5 text-xs font-medium"
            >
                <button
                    v-for="option in SHAPES"
                    :key="option.value"
                    type="button"
                    :aria-pressed="shape === option.value"
                    :class="cn(
                        'cursor-pointer rounded px-2 py-0.5 transition-colors',
                        shape === option.value
                            ? 'bg-foreground text-background'
                            : 'text-muted-foreground hover:text-foreground',
                    )"
                    @click="shape = option.value"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>

        <div
            v-if="!hasData"
            class="flex h-72 w-full items-center justify-center text-sm text-muted-foreground"
        >
            Not enough data yet.
        </div>

        <ChartContainer
            v-else
            :config="chartConfig"
            cursor
            class="h-72 w-full"
            :style="{
                '--vis-axis-tick-label-color': 'var(--muted-foreground)',
                '--vis-axis-tick-label-font-size': '11px',
                '--vis-crosshair-line-stroke-color': 'var(--muted-foreground)',
                '--vis-crosshair-line-stroke-width': '1px',
                '--vis-crosshair-line-stroke-opacity': '0.7',
                '--vis-crosshair-circle-stroke-color': 'transparent',
            }"
        >
            <!--
                `duration: 0` on every series, and it is load-bearing rather
                than a style choice. Unovis enters a shape at zero height and
                animates it to size; switching between line and bar remounts the
                components, and the transition that follows was being dropped —
                bars stayed 2px tall, the line stayed flat on the axis. Drawing
                straight to the final geometry sidesteps it.
            -->
            <VisXYContainer :data="chartData" :svg-defs="svgDefs" :y-domain="yDomain">
                <template v-if="shape === 'line'">
                    <VisArea
                        :x="xAccessor"
                        :y="yAccessors"
                        :color="areaColors"
                        :opacity="1"
                        curve-type="monotoneX"
                        :duration="0"
                    />
                    <VisLine
                        :x="xAccessor"
                        :y="yAccessors"
                        :color="lineColors"
                        :line-width="2"
                        curve-type="monotoneX"
                        :duration="0"
                    />
                </template>
                <VisGroupedBar
                    v-else
                    :x="xAccessor"
                    :y="yAccessors"
                    :color="lineColors"
                    :bar-padding="0.3"
                    :rounded-corners="2"
                    :duration="0"
                />
                <VisAxis
                    type="x"
                    :tick-format="xTickFormat"
                    :num-ticks="xNumTicks"
                    :grid-line="false"
                    :domain-line="false"
                    :tick-line="false"
                />
                <VisAxis
                    type="y"
                    :tick-format="formatCount"
                    :num-ticks="4"
                    :grid-line="false"
                    :domain-line="false"
                    :tick-line="false"
                />
                <ChartCrosshair
                    :x="xAccessor"
                    :y="yAccessors"
                    :template="tooltipTemplate"
                    :color="lineColors"
                    :circle-radius="CROSSHAIR_CIRCLE_RADIUS"
                />
                <ChartTooltip />
            </VisXYContainer>
        </ChartContainer>
    </div>
</template>
