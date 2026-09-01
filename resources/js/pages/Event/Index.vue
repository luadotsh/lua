<script setup lang="ts">
import { InfiniteScroll, router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

import EventsTable, {
    type EventData,
} from '@/components/analytics/EventsTable.vue';
import StatHeader from '@/components/analytics/StatHeader.vue';
import TimeseriesChart, {
    type TimeseriesPoint,
} from '@/components/analytics/TimeseriesChart.vue';
import { Skeleton } from '@/components/ui/skeleton';
import date from '@/date';
import AppLayout from '@/layouts/AppLayout.vue';
import type { MetricKey, Overview } from '@/lib/metrics';
import { statistics } from '@/routes/analytics';
import * as eventsRoute from '@/routes/events';

import Header from './Header.vue';

interface Table {
    data: EventData[];
    next_page_url: string | null;
}

const props = defineProps<{
    start: string;
    end: string;
    table: Table;
    hasData: boolean;
}>();

const range = ref({
    start: props.start,
    end: props.end,
    timezone: date.getUserTimezone(),
    group: 'day',
});

// The same endpoint the analytics screen reads, so the two never disagree
// about what a click or a scan is.
const metric = ref<MetricKey>('events');
const overview = ref<Overview | null>(null);
const timeseries = ref<TimeseriesPoint[]>([]);

const loadChart = async () => {
    const { data } = await axios.get(statistics.url({ query: range.value }));

    overview.value = data.overview;
    timeseries.value = data.timeseries;
};

// Mounted, not immediate: unovis measures its container on mount, and a series
// that arrives before there is a container to draw into leaves the line pinned
// flat at zero.
onMounted(loadChart);
watch(range, loadChart, { deep: true });

const refresh = (value: typeof range.value) => {
    range.value = value;
    router.visit(
        eventsRoute.index.url({
            query: {
                start: range.value.start,
                end: range.value.end,
            },
        }),
        {
            method: 'get',
            preserveState: true,
        },
    );
};
</script>

<template>
    <AppLayout title="Events" full-width>
        <template #header-actions>
            <Header :range="range" @update:range="refresh" />
        </template>

        <!-- No padding: the app already draws the card these sit in, so the
             blocks run edge to edge and are separated by rules rather than by
             a second set of rounded borders. -->
        <div class="flex min-h-0 flex-1 flex-col">
            <template v-if="overview">
                <StatHeader v-model="metric" :overview="overview" flush />
                <TimeseriesChart
                    :series="timeseries"
                    :metric="metric"
                    :group="range.group"
                    flush
                />
            </template>
            <template v-else>
                <Skeleton class="h-[104px] w-full" />
                <Skeleton class="h-[324px] w-full" />
            </template>

            <!--
                This wrapper is the only scroll container on the screen, and it
                scrolls both ways. Sideways it keeps the metrics and the chart
                still while you reach the last column; downwards it is what
                Inertia finds by walking up from `items-element`, so a wrapper
                that scrolled sideways only would swallow the infinite scroll.
                `min-w-0` is what makes the sideways part work at all: without
                it the flex child sizes to the table and pushes the overflow
                back out to the page.

                `items-element` is required — without it Inertia has nowhere to
                append the next page and it replaces the rows instead.
            -->
            <div
                class="min-h-0 min-w-0 flex-1 overflow-auto pb-px"
                data-testid="events-scroll"
            >
                <InfiniteScroll
                    data="table"
                    items-element="#events-body"
                    preserve-url
                    :buffer="300"
                >
                    <EventsTable :rows="table.data" items-id="events-body" />
                </InfiniteScroll>
            </div>
        </div>
    </AppLayout>
</template>
