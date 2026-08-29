<script setup lang="ts">
import { VisArea, VisAxis, VisLine, VisXYContainer } from '@unovis/vue';
import { computed } from 'vue';
import { ChartTooltip } from '@/components/ui/chart';
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

const VIOLET = '#8b5cf6';

const data = computed(() =>
    props.series.map((point, index) => ({
        index,
        bucket: point.bucket,
        value: Number(point[props.metric as keyof TimeseriesPoint] ?? 0),
    })),
);

const x = (d: { index: number }) => d.index;
const y = (d: { value: number }) => d.value;

// Hours and minutes need the time; days and months do not.
const bucketFormat = computed(() =>
    props.group === 'minute' || props.group === 'hour' ? 'MMM D, HH:mm' : 'MMM D',
);

const tickLabel = (index: number) => {
    const point = data.value[Math.round(index)];

    return point ? dayjs(point.bucket).format(bucketFormat.value) : '';
};

// Roughly one label per 80px of chart, so ticks never collide on a phone.
const tickCount = computed(() => Math.min(data.value.length, 7));

const peak = computed(() => Math.max(...data.value.map((d) => d.value), 0));
</script>

<template>
    <div class="rounded-lg border border-border bg-card p-4">
        <div class="mb-4 flex items-baseline justify-between gap-4">
            <h2 class="text-sm font-medium text-foreground">
                {{ metricLabels[metric] }} over time
            </h2>
            <span class="text-xs tabular-nums text-muted-foreground">
                peak {{ formatCount(peak) }}
            </span>
        </div>

        <VisXYContainer :data="data" :height="260" class="analytics-chart">
            <VisArea :x="x" :y="y" :color="VIOLET" :opacity="0.14" />
            <VisLine :x="x" :y="y" :color="VIOLET" :line-width="2" />
            <VisAxis
                type="x"
                :num-ticks="tickCount"
                :tick-format="tickLabel"
                :grid-line="false"
                :tick-line="false"
                :domain-line="false"
            />
            <VisAxis
                type="y"
                :num-ticks="4"
                :tick-format="formatCount"
                :tick-line="false"
                :domain-line="false"
            />
            <ChartTooltip />
        </VisXYContainer>
    </div>
</template>

<style scoped>
.analytics-chart {
    --vis-axis-tick-label-color: var(--muted-foreground);
    --vis-axis-grid-color: var(--border);
    --vis-axis-tick-label-font-size: 11px;
}
</style>
