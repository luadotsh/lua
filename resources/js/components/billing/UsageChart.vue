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
import { formatCount } from '@/lib/metrics';

/**
 * Usage over the billing cycle, drawn the same way as the analytics timeseries
 * so the two screens read as one product.
 */
const props = defineProps<{
    title: string;
    labels: string[];
    data: number[];
}>();

const CROSSHAIR_CIRCLE_RADIUS = 4;

const chartConfig = computed<ChartConfig>(() => ({
    value: { label: props.title, color: '#8b5cf6' },
}));

const chartData = computed(() =>
    props.labels.map((label, index) => ({
        index,
        label,
        value: Number(props.data[index] ?? 0),
    })),
);

const total = computed(() =>
    chartData.value.reduce((sum, point) => sum + point.value, 0),
);

const hasData = computed(() =>
    chartData.value.some((point) => point.value > 0),
);

const xAccessor = (d: { index: number }) => d.index;
const yAccessors = [(d: { value: number }) => d.value];

const lineColors = ['var(--color-value)'];

const gradientId = useId();
const valueGradientId = computed(() => `usage-fill-${gradientId}`);
const areaColors = computed(() => [`url(#${valueGradientId.value})`]);

const svgDefs = computed(
    () => `
    <linearGradient id="${valueGradientId.value}" x1="0" y1="0" x2="0" y2="1">
        <stop offset="5%" stop-color="var(--color-value)" stop-opacity="0.28" />
        <stop offset="95%" stop-color="var(--color-value)" stop-opacity="0.02" />
    </linearGradient>
`,
);

const xNumTicks = computed(() => Math.min(6, chartData.value.length || 1));

const xTickFormat = (tick: number): string =>
    chartData.value[Math.round(tick)]?.label ?? '';

const tooltipTemplate = (d: { label: string; value: number }): string =>
    `<div class="rounded-md border border-border bg-popover px-3 py-2 text-xs text-popover-foreground shadow-md">
        <div class="mb-1 text-muted-foreground">${d.label}</div>
        <div class="font-medium tabular-nums">${formatCount(d.value)} ${props.title.toLowerCase()}</div>
    </div>`;
</script>

<template>
    <div class="rounded-lg border border-border bg-card p-4">
        <div class="mb-2">
            <h2 class="text-sm font-medium text-foreground">
                {{ title }} this cycle
            </h2>
            <p class="text-2xl font-semibold text-foreground tabular-nums">
                {{ formatCount(total) }}
            </p>
        </div>

        <div
            v-if="!hasData"
            class="flex h-72 w-full items-center justify-center text-sm text-muted-foreground"
        >
            Nothing yet this cycle.
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
