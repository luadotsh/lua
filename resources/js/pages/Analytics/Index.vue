<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import BreakdownCard, {
    type BreakdownTab,
} from "@/components/analytics/BreakdownCard.vue";
import { browserIconUrl } from "@/lib/browsers";
import { countryFlagUrl, countryFor } from "@/lib/countries";
import { deviceIconUrl } from "@/lib/devices";
import { languageFlagUrl, languageLabel } from "@/lib/languages";
import { osIconUrl } from "@/lib/os";
import { favicon } from "@/lib/utils";
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

const countryRows = computed(
    () => breakdowns.value.locations?.find((tab) => tab.key === "country")?.rows ?? [],
);

// The map is the first tab but not the one that opens, matching clickbase.
const locationTabs = computed<BreakdownTab[]>(() => [
    { key: "map", label: "Map" },
    ...(breakdowns.value.locations ?? []),
]);

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

                <div class="grid gap-4 lg:grid-cols-2">
                    <BreakdownCard
                        title="Links"
                        empty-label="No clicks in this period."
                        :tabs="[{ key: 'links', label: 'Links', rows: links }]"
                        persist-title
                    >
                        <template #row="{ row }">
                            <img
                                :src="favicon(row.url ?? row.value)"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0 rounded-sm"
                                loading="lazy"
                                @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                            />
                            <span class="truncate">{{ row.value }}</span>
                        </template>
                    </BreakdownCard>

                    <BreakdownCard
                        title="Sources"
                        empty-label="No referrers or campaigns yet."
                        :tabs="breakdowns.sources ?? []"
                    >
                        <template #row="{ row, tab }">
                            <img
                                v-if="tab === 'referer' && row.value !== 'Direct'"
                                :src="favicon(row.value)"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0 rounded-sm"
                                loading="lazy"
                                @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                            />
                            <span class="truncate">{{ row.value }}</span>
                        </template>
                    </BreakdownCard>

                    <BreakdownCard
                        title="Locations"
                        empty-label="No location data yet."
                        :tabs="locationTabs"
                        default-tab="country"
                    >
                        <template #content-map>
                            <VisitorsMap :rows="countryRows" />
                        </template>
                        <template #row="{ row, tab }">
                            <img
                                v-if="tab === 'country' || row.country"
                                :src="countryFlagUrl(tab === 'country' ? row.value : (row.country ?? ''))"
                                alt=""
                                aria-hidden="true"
                                class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                loading="lazy"
                                @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                            />
                            <span class="truncate">
                                {{ tab === 'country' ? countryFor(row.value).name : row.value }}
                            </span>
                        </template>
                    </BreakdownCard>

                    <BreakdownCard
                        title="Devices"
                        empty-label="No device data yet."
                        :tabs="breakdowns.devices ?? []"
                    >
                        <template #row="{ row, tab }">
                            <img
                                v-if="tab === 'browser'"
                                :src="browserIconUrl(row.value)"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0"
                                loading="lazy"
                            />
                            <img
                                v-else-if="tab === 'os'"
                                :src="osIconUrl(row.value)"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0"
                                loading="lazy"
                            />
                            <img
                                v-else-if="tab === 'device' && deviceIconUrl(row.value)"
                                :src="deviceIconUrl(row.value) ?? ''"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0"
                                loading="lazy"
                                @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                            />
                            <img
                                v-else-if="tab === 'language' && languageFlagUrl(row.value)"
                                :src="languageFlagUrl(row.value) ?? ''"
                                alt=""
                                aria-hidden="true"
                                class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                loading="lazy"
                                @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                            />
                            <span class="truncate">
                                {{ tab === 'language' ? languageLabel(row.value) : row.value }}
                            </span>
                        </template>
                    </BreakdownCard>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
