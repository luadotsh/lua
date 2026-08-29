<script setup lang="ts">
import { IconArrowDownRight, IconArrowUpRight, IconMinus } from '@tabler/icons-vue';
import {
    directionOf,
    formatChange,
    formatCount,
    metricLabels,
    plottableMetrics,
    type MetricKey,
    type Overview,
} from '@/lib/metrics';
import { cn } from '@/lib/utils';

const props = defineProps<{
    overview: Overview;
    /** Metrics that are period totals rather than series, shown but not selectable. */
    summaryMetrics?: MetricKey[];
}>();

const selected = defineModel<MetricKey>({ required: true });

const isSelectable = (metric: MetricKey) => plottableMetrics.includes(metric);
</script>

<template>
    <div class="grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-border bg-border sm:grid-cols-3 lg:grid-cols-6">
        <component
            v-for="(metric, key) in overview"
            :key="key"
            :is="isSelectable(key as MetricKey) ? 'button' : 'div'"
            :type="isSelectable(key as MetricKey) ? 'button' : undefined"
            :aria-pressed="isSelectable(key as MetricKey) ? selected === key : undefined"
            :class="cn(
                'flex flex-col gap-1 bg-card p-4 text-left transition-colors',
                isSelectable(key as MetricKey) && 'cursor-pointer hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                selected === key && 'bg-accent',
            )"
            @click="isSelectable(key as MetricKey) && (selected = key as MetricKey)"
        >
            <span class="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                {{ metricLabels[key as MetricKey] }}
            </span>

            <span class="text-2xl font-semibold tabular-nums text-foreground">
                {{ formatCount(metric.value) }}
            </span>

            <span
                :class="cn(
                    'inline-flex items-center gap-1 text-xs tabular-nums',
                    directionOf(metric.change) === 'up' && 'text-emerald-600 dark:text-emerald-400',
                    directionOf(metric.change) === 'down' && 'text-rose-600 dark:text-rose-400',
                    directionOf(metric.change) === 'flat' && 'text-muted-foreground',
                )"
            >
                <IconArrowUpRight v-if="directionOf(metric.change) === 'up'" class="size-3.5" />
                <IconArrowDownRight v-else-if="directionOf(metric.change) === 'down'" class="size-3.5" />
                <IconMinus v-else class="size-3.5" />

                {{ formatChange(metric.change) }}
                <span class="text-muted-foreground">vs previous</span>
            </span>
        </component>
    </div>
</template>
