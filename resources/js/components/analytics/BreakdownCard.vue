<script setup lang="ts">
import { IconFilter } from '@tabler/icons-vue';
import { computed, ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { formatCount } from '@/lib/metrics';

export type BreakdownRow = {
    value: string;
    url?: string;
    /** The country a region or city belongs to, for its flag. */
    country?: string | null;
    events: number;
    visitors: number;
    share: number;
};

export type BreakdownTab = {
    key: string;
    label: string;
    /** Undefined while loading; a tab rendering its own content leaves it unset. */
    rows?: BreakdownRow[];
    /**
     * The dimension a row filters by. A tab without one — the map, or the link
     * list — offers no filter control at all.
     */
    filterDimension?: string;
};

const props = withDefaults(
    defineProps<{
        title: string;
        emptyLabel: string;
        tabs: BreakdownTab[];
        /** Which tab opens first, when it should not be the first listed. */
        defaultTab?: string;
        /** Keep the title visible even when the tab list could stand in for it. */
        persistTitle?: boolean;
        /**
         * Edge to edge, with only the dividers between cards. For screens whose
         * content already sits inside the app's own card, where a second rounded
         * border around this one is a border too many.
         */
        flush?: boolean;
        skeletonRows?: number;
    }>(),
    { skeletonRows: 5 },
);

const emit = defineEmits<{
    filter: [payload: { dimension: string; row: BreakdownRow }];
}>();

const activeTab = ref(props.defaultTab ?? props.tabs[0]?.key ?? '');

// "Direct" is a label the dashboard prints for an absent referrer, not a value
// any column holds, so filtering by it would return nothing.
const isFilterable = (tab: BreakdownTab, row: BreakdownRow): boolean =>
    Boolean(tab.filterDimension) && row.value !== '' && row.value !== 'Direct';

const applyFilter = (tab: BreakdownTab, row: BreakdownRow): void => {
    if (!tab.filterDimension || !isFilterable(tab, row)) {
        return;
    }

    emit('filter', { dimension: tab.filterDimension, row });
};

// A single tab needs no tab list — the title carries the card instead.
const showTabList = computed(() => props.tabs.length > 1);
</script>

<template>
    <Card
        :class="cn(
            'gap-0 py-3',
            flush && 'rounded-none border-0 py-4 shadow-none',
        )"
    >
        <!-- With tabs visible the list plays the title's role, so the heading
             only stays for screen readers. -->
        <CardHeader
            v-if="!showTabList || persistTitle"
            :class="flush ? 'px-4' : 'px-3'"
        >
            <CardTitle>{{ title }}</CardTitle>
        </CardHeader>

        <CardContent :class="flush ? 'px-4' : 'px-3'">
            <Tabs v-model="activeTab">
                <div
                    v-if="showTabList"
                    class="mb-3 flex h-9 items-center justify-between gap-2"
                >
                    <TabsList :aria-label="title">
                        <TabsTrigger
                            v-for="tab in tabs"
                            :key="tab.key"
                            :value="tab.key"
                        >
                            {{ tab.label }}
                        </TabsTrigger>
                    </TabsList>
                </div>

                <TabsContent v-for="tab in tabs" :key="tab.key" :value="tab.key">
                    <slot
                        v-if="$slots[`content-${tab.key}`]"
                        :name="`content-${tab.key}`"
                        :rows="tab.rows"
                    />

                    <ol v-else-if="tab.rows === undefined" class="space-y-1">
                        <li v-for="n in skeletonRows" :key="n">
                            <Skeleton class="h-8 w-full" />
                        </li>
                    </ol>

                    <p
                        v-else-if="tab.rows.length === 0"
                        class="py-6 text-center text-sm text-muted-foreground"
                    >
                        {{ emptyLabel }}
                    </p>

                    <!-- group/report: hovering anywhere in the list reveals every
                         row's share at once, rather than one at a time. -->
                    <ol v-else class="group/report space-y-1">
                        <li
                            v-for="row in tab.rows"
                            :key="row.value"
                            class="group relative flex h-8 min-h-8 items-center justify-between gap-3 overflow-hidden rounded-md px-2 text-sm transition-colors hover:bg-muted"
                        >
                            <span
                                class="absolute inset-y-0 left-0 bg-primary/10"
                                :style="{ width: `${row.share}%` }"
                                aria-hidden="true"
                            />

                            <span class="relative z-10 flex min-w-0 items-center gap-2">
                                <slot name="row" :row="row" :tab="tab.key">
                                    <span class="truncate">{{ row.value }}</span>
                                </slot>
                            </span>

                            <span
                                class="relative z-10 flex shrink-0 items-center justify-end tabular-nums"
                            >
                                <TooltipProvider v-if="isFilterable(tab, row)">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <button
                                                type="button"
                                                class="mr-1 flex size-5 shrink-0 cursor-pointer items-center justify-center rounded border border-border text-muted-foreground opacity-0 transition-opacity hover:text-foreground focus-visible:opacity-100 focus-visible:outline-none group-hover:opacity-100"
                                                :aria-label="`Filter by ${row.value}`"
                                                @click="applyFilter(tab, row)"
                                            >
                                                <IconFilter class="size-3" />
                                            </button>
                                        </TooltipTrigger>
                                        <TooltipContent side="left">
                                            Filter by {{ row.value }}
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <!-- The count sits flush right, translated over the
                                     hidden share cell, and slides back to reveal it
                                     when the list is hovered. -->
                                <span
                                    class="pointer-events-none translate-x-[3rem] text-muted-foreground transition-transform duration-200 ease-out group-hover/report:translate-x-0"
                                >
                                    {{ formatCount(row.events) }}
                                </span>
                                <span
                                    class="pointer-events-none w-12 shrink-0 pl-2 text-right text-xs text-muted-foreground opacity-0 transition-opacity duration-200 group-hover/report:opacity-100"
                                >
                                    {{ row.share }}%
                                </span>
                            </span>
                        </li>
                    </ol>
                </TabsContent>
            </Tabs>
        </CardContent>
    </Card>
</template>
