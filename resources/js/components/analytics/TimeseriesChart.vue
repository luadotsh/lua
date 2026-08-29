<script setup lang="ts">
import { VisArea, VisAxis, VisLine, VisXYContainer } from '@unovis/vue';
import { useId } from 'reka-ui';
import { computed } from 'vue';
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    type ChartConfig,
} from '@/components/ui/chart';
import dayjs from '@/dayjs';
import { formatCount, metricLabels, type MetricKey } from '@/lib/metrics';

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

const chartConfig = computed<ChartConfig>(() => ({
    value: { label: metricLabels[props.metric], color: '#8b5cf6' },
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

const lineColors = ['var(--color-value)'];

// A unique id per instance, so two charts on one page never collide on the
// <defs> they inject.
const gradientId = useId();
const valueGradientId = computed(() => `value-fill-${gradientId}`);
const areaColors = computed(() => [`url(#${valueGradientId.value})`]);

// A soft vertical fade from the series colour down to almost transparent.
const svgDefs = computed(
    () => `
    <linearGradient id="${valueGradientId.value}" x1="0" y1="0" x2="0" y2="1">
        <stop offset="5%" stop-color="var(--color-value)" stop-opacity="0.28" />
        <stop offset="95%" stop-color="var(--color-value)" stop-opacity="0.02" />
    </linearGradient>
`,
);

// At most six labels on the x axis, however many points are plotted.
const xNumTicks = computed(() => Math.min(6, chartData.value.length || 1));

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
        <h2 class="mb-2 text-sm font-medium text-foreground">
            {{ metricLabels[metric] }} over time
        </h2>

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
            <!-- No margin override: the plot runs edge to edge and unovis still
                 reserves the gutter the y labels need. -->
            <VisXYContainer :data="chartData" :svg-defs="svgDefs">
                <VisArea
                    :x="xAccessor"
                    :y="yAccessors"
                    :color="areaColors"
                    :opacity="1"
                    curve-type="monotoneX"
                />
                <VisLine
                    :x="xAccessor"
                    :y="yAccessors"
                    :color="lineColors"
                    :line-width="2"
                    curve-type="monotoneX"
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
                    :template="tooltipTemplate"
                    :color="lineColors"
                    :circle-radius="CROSSHAIR_CIRCLE_RADIUS"
                />
                <ChartTooltip />
            </VisXYContainer>
        </ChartContainer>
    </div>
</template>
