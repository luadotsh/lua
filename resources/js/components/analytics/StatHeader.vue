<script setup lang="ts">
import { IconArrowDownRight, IconArrowUpRight, IconMinus } from '@tabler/icons-vue';
import {
    directionOf,
    formatChange,
    formatCount,
    metricLabels,
    type MetricKey,
    type Overview,
} from '@/lib/metrics';
import { cn } from '@/lib/utils';

defineProps<{
    overview: Overview;
}>();

const selected = defineModel<MetricKey>({ required: true });
</script>

<template>
    <div class="grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-border bg-border lg:grid-cols-4">
        <button
            v-for="(metric, key) in overview"
            :key="key"
            type="button"
            :aria-pressed="selected === key"
            :class="cn(
                'flex cursor-pointer flex-col gap-1 bg-card p-4 text-left transition-colors',
                'hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset',
                selected === key && 'bg-accent',
            )"
            @click="selected = key as MetricKey"
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
        </button>
    </div>
</template>
