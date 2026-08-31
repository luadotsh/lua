<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';

import BreakdownCard, {
    type BreakdownRow,
    type BreakdownTab,
} from '@/components/analytics/BreakdownCard.vue';
import FilterPills, {
    type ActiveFilter,
} from '@/components/analytics/FilterPills.vue';
import StatHeader from '@/components/analytics/StatHeader.vue';
import TimeseriesChart, {
    type TimeseriesPoint,
} from '@/components/analytics/TimeseriesChart.vue';
import VisitorsMap from '@/components/analytics/VisitorsMap.vue';
import RangePicker from '@/components/RangePicker.vue';
import { Skeleton } from '@/components/ui/skeleton';
import date from '@/date';
import AppLayout from '@/layouts/AppLayout.vue';
import { browserIconUrl } from '@/lib/browsers';
import { countryFlagUrl, countryFor } from '@/lib/countries';
import { deviceIconUrl } from '@/lib/devices';
import { languageFlagUrl, languageLabel } from '@/lib/languages';
import type { MetricKey, Overview } from '@/lib/metrics';
import { osIconUrl } from '@/lib/os';
import { favicon } from '@/lib/utils';
import { index as analyticsRoute, statistics } from '@/routes/analytics';

interface Range {
    timezone: string;
    group: string;
    start: string;
    end: string;
}

const props = defineProps<{
    start: string;
    end: string;
    filters: ActiveFilter[];
}>();

type Breakdowns = Record<string, BreakdownTab[]>;

const range = ref<Range>({
    timezone: date.getUserTimezone(),
    group: 'day',
    start: props.start,
    end: props.end,
});

// The URL is the filter state. Every refine is a real visit, so a narrowed
// dashboard can be bookmarked, shared and walked back out of with the browser's
// own back button.
const filterQuery = computed(() => {
    const query: Record<string, string | string[]> = {};

    for (const filter of props.filters) {
        if (filter.values.length === 1) {
            query[filter.dimension] = filter.values[0]!;
        } else if (filter.values.length > 1) {
            query[filter.dimension] = [...filter.values];
        }
    }

    return query;
});

const navigate = (query: Record<string, string | string[]>) => {
    router.get(
        analyticsRoute.url({
            query: { start: range.value.start, end: range.value.end, ...query },
        }),
        {},
        // preserveState keeps each card on the tab you were reading and the
        // header on the metric you had selected; without it a refine snaps
        // every one of them back to its first.
        { preserveState: true, preserveScroll: true },
    );
};

const applyFilter = ({
    dimension,
    row,
}: {
    dimension: string;
    row: BreakdownRow;
}) => {
    const query = { ...filterQuery.value, [dimension]: row.value };

    // A region or city row carries the country it belongs to. Applying that as
    // its own filter keeps two same-named cities in different countries apart.
    if ((dimension === 'region' || dimension === 'city') && row.country) {
        query.country = row.country;
    }

    navigate(query);
};

const removeFilter = (dimension: string) => {
    const query = { ...filterQuery.value };
    delete query[dimension];

    navigate(query);
};

const metric = ref<MetricKey>('events');
const loading = ref(true);
const overview = ref<Overview | null>(null);
const timeseries = ref<TimeseriesPoint[]>([]);
const links = ref<BreakdownTab['rows']>([]);
const breakdowns = ref<Breakdowns>({});

const load = async () => {
    loading.value = true;

    try {
        const { data } = await axios.get(
            statistics.url({ query: { ...range.value, ...filterQuery.value } }),
        );

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
watch(() => props.filters, load, { deep: true });

const countryRows = computed(
    () =>
        breakdowns.value.locations?.find((tab) => tab.key === 'country')
            ?.rows ?? [],
);

// The map is the first tab but not the one that opens, matching clickbase.
const locationTabs = computed<BreakdownTab[]>(() => [
    { key: 'map', label: 'Map' },
    ...(breakdowns.value.locations ?? []),
]);

// A tab's key is its dimension, so every tab the server sends filters by
// itself. The map is the exception: it renders a slot, not rows.
const filterable = (tabs: BreakdownTab[]): BreakdownTab[] =>
    tabs.map((tab) =>
        tab.key === 'map' ? tab : { ...tab, filterDimension: tab.key },
    );

const setRange = (next: Range) => {
    range.value = { ...next, timezone: date.getUserTimezone() };
};
</script>

<template>
    <Head title="Analytics" />

    <AppLayout title="Analytics" full-width>
        <template #header-actions>
            <RangePicker
                v-model:range="range"
                @update:range="setRange"
                placement="bottom-end"
            />
        </template>

        <!--
            No padding and no rounded borders: the app already draws the card
            these blocks sit in, so they run edge to edge and are separated by
            rules rather than by a second set of borders — the same language as
            the events screen.

            The shell stops scrolling for a full-width screen, so the content
            owns its own scrolling here. Everything scrolls together: unlike the
            events table there is no header row to pin, and freezing the metrics
            and chart would leave the breakdowns a few hundred pixels to live in.
        -->
        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-auto">
            <template v-if="loading && !overview">
                <Skeleton class="h-[104px] w-full" />
                <Skeleton class="h-[324px] w-full" />
                <div class="grid gap-px bg-border lg:grid-cols-2">
                    <Skeleton v-for="i in 4" :key="i" class="h-64 w-full" />
                </div>
            </template>

            <template v-else-if="overview">
                <FilterPills
                    :filters="filters"
                    @remove="removeFilter"
                    @clear="navigate({})"
                />

                <StatHeader v-model="metric" :overview="overview" flush />

                <TimeseriesChart
                    :series="timeseries"
                    :metric="metric"
                    :group="range.group"
                    flush
                />

                <div class="grid gap-px bg-border lg:grid-cols-2">
                    <BreakdownCard
                        title="Links"
                        empty-label="No clicks in this period."
                        :tabs="[{ key: 'links', label: 'Links', rows: links }]"
                        persist-title
                        flush
                    >
                        <template #row="{ row }">
                            <img
                                :src="favicon(row.url ?? row.value)"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0 rounded-sm"
                                loading="lazy"
                                @error="
                                    (event) =>
                                        ((
                                            event.target as HTMLImageElement
                                        ).style.display = 'none')
                                "
                            />
                            <span class="truncate">{{ row.value }}</span>
                        </template>
                    </BreakdownCard>

                    <BreakdownCard
                        title="Sources"
                        empty-label="No referrers or campaigns yet."
                        :tabs="filterable(breakdowns.sources ?? [])"
                        flush
                        @filter="applyFilter"
                    >
                        <template #row="{ row, tab }">
                            <img
                                v-if="
                                    tab === 'referer' && row.value !== 'Direct'
                                "
                                :src="favicon(row.value)"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0 rounded-sm"
                                loading="lazy"
                                @error="
                                    (event) =>
                                        ((
                                            event.target as HTMLImageElement
                                        ).style.display = 'none')
                                "
                            />
                            <span class="truncate">{{ row.value }}</span>
                        </template>
                    </BreakdownCard>

                    <BreakdownCard
                        title="Locations"
                        empty-label="No location data yet."
                        :tabs="filterable(locationTabs)"
                        default-tab="country"
                        flush
                        @filter="applyFilter"
                    >
                        <template #content-map>
                            <VisitorsMap :rows="countryRows" />
                        </template>
                        <template #row="{ row, tab }">
                            <img
                                v-if="tab === 'country' || row.country"
                                :src="
                                    countryFlagUrl(
                                        tab === 'country'
                                            ? row.value
                                            : (row.country ?? ''),
                                    )
                                "
                                alt=""
                                aria-hidden="true"
                                class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                loading="lazy"
                                @error="
                                    (event) =>
                                        ((
                                            event.target as HTMLImageElement
                                        ).style.display = 'none')
                                "
                            />
                            <span class="truncate">
                                {{
                                    tab === 'country'
                                        ? countryFor(row.value).name
                                        : row.value
                                }}
                            </span>
                        </template>
                    </BreakdownCard>

                    <BreakdownCard
                        title="Devices"
                        empty-label="No device data yet."
                        :tabs="filterable(breakdowns.devices ?? [])"
                        flush
                        @filter="applyFilter"
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
                                v-else-if="
                                    tab === 'device' && deviceIconUrl(row.value)
                                "
                                :src="deviceIconUrl(row.value) ?? ''"
                                alt=""
                                aria-hidden="true"
                                class="size-4 shrink-0"
                                loading="lazy"
                                @error="
                                    (event) =>
                                        ((
                                            event.target as HTMLImageElement
                                        ).style.display = 'none')
                                "
                            />
                            <img
                                v-else-if="
                                    tab === 'language' &&
                                    languageFlagUrl(row.value)
                                "
                                :src="languageFlagUrl(row.value) ?? ''"
                                alt=""
                                aria-hidden="true"
                                class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                loading="lazy"
                                @error="
                                    (event) =>
                                        ((
                                            event.target as HTMLImageElement
                                        ).style.display = 'none')
                                "
                            />
                            <span class="truncate">
                                {{
                                    tab === 'language'
                                        ? languageLabel(row.value)
                                        : row.value
                                }}
                            </span>
                        </template>
                    </BreakdownCard>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
