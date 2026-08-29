<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import BreakdownCard, {
    type BreakdownTab,
} from "@/components/analytics/BreakdownCard.vue";
import StatHeader from "@/components/analytics/StatHeader.vue";
import TimeseriesChart, {
    type TimeseriesPoint,
} from "@/components/analytics/TimeseriesChart.vue";
import VisitorsMap from "@/components/analytics/VisitorsMap.vue";
import RangePicker from "@/components/RangePicker.vue";
import { Skeleton } from "@/components/ui/skeleton";
import date from "@/date";
import AppLayout from "@/layouts/AppLayout.vue";
import type { MetricKey, Overview } from "@/lib/metrics";
import { statistics } from "@/routes/analytics";

interface Range {
    timezone: string;
    group: string;
    start: string;
    end: string;
}

type Breakdowns = Record<string, BreakdownTab[]>;

const range = ref<Range>({
    timezone: date.getUserTimezone(),
    group: "day",
    start: usePage().props.start as string,
    end: usePage().props.end as string,
});

const metric = ref<MetricKey>("events");
const loading = ref(true);
const overview = ref<Overview | null>(null);
const timeseries = ref<TimeseriesPoint[]>([]);
const links = ref<BreakdownTab["rows"]>([]);
const breakdowns = ref<Breakdowns>({});

const load = async () => {
    loading.value = true;

    try {
        const { data } = await axios.get(statistics.url({ query: range.value }));

        overview.value = data.overview;
        timeseries.value = data.timeseries;
        links.value = data.links;
        breakdowns.value = data.breakdowns;
    } finally {
        loading.value = false;
    }
};

onMounted(load);
watch(range, load, { deep: true });

// The map and the Locations card read the same country rows.
const countryRows = computed(
    () => breakdowns.value.locations?.find((tab) => tab.key === "country")?.rows ?? [],
);

const setRange = (next: Range) => {
    range.value = { ...next, timezone: date.getUserTimezone() };
};
</script>

<template>
    <Head title="Analytics" />

    <AppLayout>
        <template #header-right>
            <RangePicker
                v-model:range="range"
                @update:range="setRange"
                placement="bottom-end"
            />
        </template>

        <div class="flex flex-col gap-4 p-4 lg:p-6">
            <h1 class="page-title">Analytics</h1>

            <template v-if="loading && !overview">
                <Skeleton class="h-[104px] w-full rounded-lg" />
                <Skeleton class="h-[324px] w-full rounded-lg" />
                <Skeleton class="h-[368px] w-full rounded-lg" />
                <Skeleton class="h-[300px] w-full rounded-lg" />
                <div class="grid gap-4 lg:grid-cols-2">
                    <Skeleton v-for="i in 4" :key="i" class="h-64 w-full rounded-lg" />
                </div>
            </template>

            <template v-else-if="overview">
                <StatHeader v-model="metric" :overview="overview" />

                <TimeseriesChart
                    :series="timeseries"
                    :metric="metric"
                    :group="range.group"
                />

                <VisitorsMap :rows="countryRows" />

                <div class="grid gap-4 lg:grid-cols-2">
                    <BreakdownCard
                        title="Links"
                        empty-label="No clicks in this period."
                        :tabs="[{ key: 'links', label: 'Links', rows: links, icon: 'favicon' }]"
                    />
                    <BreakdownCard
                        title="Sources"
                        empty-label="No referrers or campaigns yet."
                        :tabs="breakdowns.sources ?? []"
                    />
                    <BreakdownCard
                        title="Locations"
                        empty-label="No location data yet."
                        :tabs="breakdowns.locations ?? []"
                    />
                    <BreakdownCard
                        title="Devices"
                        empty-label="No device data yet."
                        :tabs="breakdowns.devices ?? []"
                    />
                </div>
            </template>
        </div>
    </AppLayout>
</template>
