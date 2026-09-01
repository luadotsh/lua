<script setup lang="ts">
import type { Component } from 'vue';

import { formatNumber } from '@/lib/metrics';

/**
 * One resource against its plan limit. The bar is the point: the number alone
 * says nothing about how close to the ceiling the workspace is.
 */
defineProps<{
    icon: Component;
    label: string;
    used: number;
    remaining: number;
    limit: number;
    percent: number;
}>();
</script>

<template>
    <div class="flex flex-col gap-3 bg-card p-4">
        <div class="flex items-center gap-2">
            <component :is="icon" class="size-4 text-muted-foreground" />
            <span
                class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
            >
                {{ label }}
            </span>
        </div>

        <span class="text-2xl font-semibold text-foreground tabular-nums">
            {{ formatNumber(used) }}
        </span>

        <div class="flex flex-col gap-1.5">
            <div class="overflow-hidden rounded-full bg-muted">
                <div
                    class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600 transition-all"
                    :style="{ width: `${Math.min(percent, 100)}%` }"
                />
            </div>
            <span class="text-xs text-muted-foreground tabular-nums">
                {{ formatNumber(remaining) }} remaining of
                {{ formatNumber(limit) }}
            </span>
        </div>
    </div>
</template>
