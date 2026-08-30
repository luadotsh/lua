<script setup lang="ts">
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { InfiniteScroll, router } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import date from "@/date";

import * as eventsRoute from "@/routes/events";

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import StatHeader from "@/components/analytics/StatHeader.vue";
import TimeseriesChart, {
    type TimeseriesPoint,
} from "@/components/analytics/TimeseriesChart.vue";
import { Skeleton } from "@/components/ui/skeleton";
import type { MetricKey, Overview } from "@/lib/metrics";
import { statistics } from "@/routes/analytics";
import Header from "./Header.vue";

interface EventLink {
    link: string;
}

interface EventData {
    id: string | number;
    event: string;
    link: EventLink;
    country: string;
    region: string;
    city: string;
    device: string;
    browser: string;
    os: string;
    created_at: string;
    language: string;
    utm_medium: string;
    utm_source: string;
    utm_campaign: string;
    utm_content: string;
    utm_term: string;
    referer: string;
}

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
    group: "day",
});

// Every column, always. The picker that used to hide most of them was one more
// thing to configure before the screen told you anything.
const COLUMNS = [
    { key: "event", label: "Event" },
    { key: "link", label: "Link" },
    { key: "country", label: "Country" },
    { key: "region", label: "Region" },
    { key: "city", label: "City" },
    { key: "device", label: "Device" },
    { key: "browser", label: "Browser" },
    { key: "os", label: "OS" },
    { key: "date", label: "Date" },
    { key: "language", label: "Language" },
    { key: "utm_source", label: "UTM source" },
    { key: "utm_medium", label: "UTM medium" },
    { key: "utm_campaign", label: "UTM campaign" },
    { key: "utm_content", label: "UTM content" },
    { key: "utm_term", label: "UTM term" },
    { key: "referer", label: "Referrer" },
] as const;

// The same endpoint the analytics screen reads, so the two never disagree
// about what a click or a scan is.
const metric = ref<MetricKey>("events");
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
            method: "get",
            preserveState: true,
        }
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
        <div class="flex flex-col">
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
                The sideways scroll belongs to the table alone: sixteen columns
                do not fit, but the metrics and the chart above should stay put
                while you reach the last of them.

                `items-element` is required — without it Inertia has nowhere to
                append the next page and it replaces the rows instead.
            -->
            <InfiniteScroll data="table" items-element="#events-body" preserve-url>
                <div class="w-full overflow-x-auto">
                <Table>
                    <TableHeader sticky>
                        <TableRow>
                            <TableHead
                                v-for="column in COLUMNS"
                                :key="column.key"
                                class="whitespace-nowrap"
                            >
                                {{ column.label }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody id="events-body">
                        <TableRow v-for="event in table.data" :key="event.id">
                            <TableCell class="whitespace-nowrap">{{ event.event }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.link.link }}</TableCell>
                            <TableCell>{{ event.country }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.region }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.city }}</TableCell>
                            <TableCell>{{ event.device }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.browser }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.os }}</TableCell>
                            <TableCell class="whitespace-nowrap">
                                {{ date.formatDateTime(event.created_at) }}
                            </TableCell>
                            <TableCell>{{ event.language }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_source }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_medium }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_campaign }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_content }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_term }}</TableCell>
                            <TableCell class="max-w-xs truncate">{{ event.referer }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                </div>
            </InfiniteScroll>
        </div>
    </AppLayout>
</template>
