<script setup lang="ts">
import { computed, ref } from 'vue';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { browserIconUrl } from '@/lib/browsers';
import { countryFlagUrl, countryFor } from '@/lib/countries';
import { deviceIconUrl } from '@/lib/devices';
import { formatCount } from '@/lib/metrics';
import { osIconUrl } from '@/lib/os';
import { favicon } from '@/lib/utils';

export type BreakdownRow = {
    value: string;
    url?: string;
    events: number;
    visitors: number;
    share: number;
};

export type BreakdownTab = {
    key: string;
    label: string;
    rows: BreakdownRow[];
    /** Which icon set, if any, sits beside each row. */
    icon?: 'country' | 'browser' | 'os' | 'device' | 'favicon';
};

const props = defineProps<{
    title: string;
    tabs: BreakdownTab[];
    emptyLabel: string;
}>();

const active = ref(props.tabs[0]?.key ?? '');

const current = computed(
    () => props.tabs.find((tab) => tab.key === active.value) ?? props.tabs[0],
);

const iconFor = (row: BreakdownRow, kind?: BreakdownTab['icon']): string | null => {
    switch (kind) {
        case 'country':
            return countryFlagUrl(row.value);
        case 'browser':
            return browserIconUrl(row.value);
        case 'os':
            return osIconUrl(row.value);
        case 'device':
            return deviceIconUrl(row.value);
        case 'favicon':
            return favicon(row.url ?? row.value);
        default:
            return null;
    }
};

// Country codes are not readable on their own.
const labelFor = (row: BreakdownRow, kind?: BreakdownTab['icon']): string =>
    kind === 'country' ? countryFor(row.value).name : row.value;
</script>

<template>
    <section class="flex flex-col rounded-lg border border-border bg-card">
        <header class="flex items-center justify-between gap-4 border-b border-border px-4 py-3">
            <h2 class="text-sm font-medium text-foreground">{{ title }}</h2>

            <Tabs v-if="tabs.length > 1" v-model="active">
                <TabsList class="h-7">
                    <TabsTrigger
                        v-for="tab in tabs"
                        :key="tab.key"
                        :value="tab.key"
                        class="px-2 text-xs"
                    >
                        {{ tab.label }}
                    </TabsTrigger>
                </TabsList>
            </Tabs>
        </header>

        <div v-if="current?.rows.length" class="flex flex-col">
            <div
                v-for="row in current.rows"
                :key="row.value"
                class="relative flex items-center gap-3 px-4 py-2 text-sm"
            >
                <!-- The share reads as a bar behind the row, so scanning the
                     column shows the distribution without a second chart. -->
                <div
                    class="absolute inset-y-0 left-0 bg-violet-500/10"
                    :style="{ width: `${row.share}%` }"
                    aria-hidden="true"
                />

                <img
                    v-if="iconFor(row, current.icon)"
                    :src="iconFor(row, current.icon) ?? undefined"
                    :alt="''"
                    class="relative size-4 shrink-0 rounded-sm object-contain"
                    loading="lazy"
                />

                <span class="relative min-w-0 flex-1 truncate text-foreground" :title="labelFor(row, current.icon)">
                    {{ labelFor(row, current.icon) }}
                </span>

                <span class="relative shrink-0 tabular-nums text-muted-foreground">
                    {{ formatCount(row.visitors) }}
                </span>
                <span class="relative w-14 shrink-0 text-right tabular-nums font-medium text-foreground">
                    {{ formatCount(row.events) }}
                </span>
            </div>
        </div>

        <p v-else class="px-4 py-8 text-center text-sm text-muted-foreground">
            {{ emptyLabel }}
        </p>
    </section>
</template>
