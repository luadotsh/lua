<script setup lang="ts">
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
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
import Pagination from "@/components/Pagination.vue";
import StatHeader from "@/components/analytics/StatHeader.vue";
import TimeseriesChart, {
    type TimeseriesPoint,
} from "@/components/analytics/TimeseriesChart.vue";
import { Skeleton } from "@/components/ui/skeleton";
import type { MetricKey, Overview } from "@/lib/metrics";
import { statistics } from "@/routes/analytics";
import Header from "./Header.vue";

interface Column {
    key: string;
    label: string;
    show: boolean;
}

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

const columns = ref<Column[]>([
    { key: "event", label: "Event", show: true },
    { key: "link", label: "Link", show: true },
    { key: "country", label: "Country", show: true },
    { key: "region", label: "Region", show: true },
    { key: "city", label: "City", show: false },
    { key: "device", label: "Device", show: true },
    { key: "browser", label: "Browser", show: true },
    { key: "os", label: "OS", show: true },
    { key: "date", label: "Date", show: true },
    { key: "language", label: "Language", show: false },
    { key: "utm_medium", label: "Utm Medium", show: false },
    { key: "utm_source", label: "Utm Source", show: false },
    { key: "utm_campaign", label: "Utm Campaign", show: false },
    { key: "utm_content", label: "Utm Content", show: false },
    { key: "utm_term", label: "Utm Term", show: false },
    { key: "referer", label: "Referer", show: false },
]);

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
    <AppLayout title="Events">
        <template #header-actions>
            <Header
                :columns="columns"
                :range="range"
                @update:columns="columns = $event"
                @update:range="refresh"
            />
        </template>

        <div class="flex flex-col gap-4 p-4 lg:p-6">
            <template v-if="overview">
                <StatHeader v-model="metric" :overview="overview" />
                <TimeseriesChart
                    :series="timeseries"
                    :metric="metric"
                    :group="range.group"
                />
            </template>
            <template v-else>
                <Skeleton class="h-[104px] w-full rounded-lg" />
                <Skeleton class="h-[324px] w-full rounded-lg" />
            </template>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead v-for="column in columns.filter((c) => c.show)" :key="column.key">
                                {{ column.label }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="event in table.data" :key="event.id">
                            <TableCell v-if="columns.find((c) => c.key === 'event' && c.show)">{{ event.event }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'link' && c.show)">{{ event.link.link }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'country' && c.show)">{{ event.country }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'region' && c.show)">{{ event.region }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'city' && c.show)">{{ event.city }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'device' && c.show)">{{ event.device }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'browser' && c.show)">{{ event.browser }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'os' && c.show)">{{ event.os }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'date' && c.show)">{{ date.formatDateTime(event.created_at) }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'language' && c.show)">{{ event.language }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'utm_medium' && c.show)">{{ event.utm_medium }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'utm_source' && c.show)">{{ event.utm_source }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'utm_campaign' && c.show)">{{ event.utm_campaign }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'utm_content' && c.show)">{{ event.utm_content }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'utm_term' && c.show)">{{ event.utm_term }}</TableCell>
                            <TableCell v-if="columns.find((c) => c.key === 'referer' && c.show)">{{ event.referer }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <template v-if="table.next_page_url" #pagination>
            <Pagination :data="table" />
        </template>
    </AppLayout>
</template>
