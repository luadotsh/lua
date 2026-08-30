<script setup lang="ts">
import { Head, InfiniteScroll, router } from "@inertiajs/vue3";
import { IconPencil } from "@tabler/icons-vue";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import BreakdownCard, {
    type BreakdownTab,
} from "@/components/analytics/BreakdownCard.vue";
import EventsTable, {
    type EventData,
} from "@/components/analytics/EventsTable.vue";
import StatHeader from "@/components/analytics/StatHeader.vue";
import TimeseriesChart, {
    type TimeseriesPoint,
} from "@/components/analytics/TimeseriesChart.vue";
import VisitorsMap from "@/components/analytics/VisitorsMap.vue";
import RangePicker from "@/components/RangePicker.vue";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import date from "@/date";
import AppLayout from "@/layouts/AppLayout.vue";
import { browserIconUrl } from "@/lib/browsers";
import { countryFlagUrl, countryFor } from "@/lib/countries";
import { deviceIconUrl } from "@/lib/devices";
import { languageFlagUrl, languageLabel } from "@/lib/languages";
import type { MetricKey, Overview } from "@/lib/metrics";
import { osIconUrl } from "@/lib/os";
import { favicon } from "@/lib/utils";
import { statistics } from "@/routes/analytics";
import * as linksRoute from "@/routes/links";

interface LinkData {
    id: string;
    link: string;
    url: string;
}

interface Table {
    data: EventData[];
    next_page_url: string | null;
}

const props = defineProps<{
    link: LinkData;
    start: string;
    end: string;
    table: Table;
}>();

const range = ref({
    start: props.start,
    end: props.end,
    timezone: date.getUserTimezone(),
    group: "day",
});

const metric = ref<MetricKey>("events");
const loading = ref(true);
const overview = ref<Overview | null>(null);
const timeseries = ref<TimeseriesPoint[]>([]);
const breakdowns = ref<Record<string, BreakdownTab[]>>({});

// The workspace dashboard's own endpoint, narrowed to this link. `link` is a
// filter like any other, so nothing here needs a second implementation of what
// a click is.
const load = async () => {
    loading.value = true;

    try {
        const { data } = await axios.get(
            statistics.url({ query: { ...range.value, link: props.link.id } }),
        );

        overview.value = data.overview;
        timeseries.value = data.timeseries;
        breakdowns.value = data.breakdowns;
    } finally {
        loading.value = false;
    }
};

onMounted(load);
watch(range, load, { deep: true });

// The map opens as a tab of the locations card, not as a card of its own.
const locationTabs = computed<BreakdownTab[]>(() => [
    { key: "map", label: "Map" },
    ...(breakdowns.value.locations ?? []),
]);

const countryRows = computed(
    () => breakdowns.value.locations?.find((tab) => tab.key === "country")?.rows ?? [],
);

const breadcrumbs = computed(() => [
    { title: "Links", href: linksRoute.index.url() },
    { title: props.link.link, href: linksRoute.show.url(props.link.id) },
]);

// The range lives in the URL so a period worth showing someone is a link.
const setRange = (value: typeof range.value) => {
    range.value = value;

    router.visit(
        linksRoute.show.url(props.link.id, {
            query: { start: value.start, end: value.end },
        }),
        { method: "get", preserveState: true, preserveScroll: true },
    );
};
</script>

<template>
    <Head :title="link.link" />

    <AppLayout :breadcrumbs="breadcrumbs" full-width>
        <template #header-actions>
            <div class="flex items-center gap-2">
                <RangePicker
                    :range="range"
                    placement="bottom-end"
                    @update:range="setRange"
                />
                <Button variant="outline" as-child>
                    <a :href="linksRoute.edit.url(link.id)">
                        <IconPencil class="size-4" />
                        Edit
                    </a>
                </Button>
            </div>
        </template>

        <!--
            Edge to edge and separated by rules, like the events screen: the app
            already draws the card these blocks sit in.

            Unlike events, the whole page scrolls. The metrics, the chart and
            the three breakdown cards come to more than a viewport on their own,
            so pinning them would leave the table no height at all.
        -->
        <div
            class="flex min-h-0 min-w-0 flex-1 flex-col overflow-y-auto pb-px"
            data-testid="link-events-scroll"
        >
            <div class="flex items-center gap-2 border-b border-border bg-card px-4 py-3">
                <img
                    :src="favicon(link.url)"
                    alt=""
                    aria-hidden="true"
                    class="size-4 shrink-0 rounded-sm"
                    loading="lazy"
                    @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                />
                <a
                    :href="link.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="truncate text-sm text-muted-foreground hover:underline"
                >
                    {{ link.url }}
                </a>
            </div>

            <template v-if="loading && !overview">
                <Skeleton class="h-[104px] w-full" />
                <Skeleton class="h-[324px] w-full" />
            </template>

            <template v-else-if="overview">
                <StatHeader v-model="metric" :overview="overview" flush />

                <TimeseriesChart
                    :series="timeseries"
                    :metric="metric"
                    :group="range.group"
                    flush
                />

                <div class="grid gap-px border-b border-border bg-border lg:grid-cols-3">
                    <BreakdownCard
                        title="Sources"
                        empty-label="No referrers or campaigns yet."
                        :tabs="breakdowns.sources ?? []"
                        flush
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
                        flush
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
                        flush
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

            <InfiniteScroll
                data="table"
                items-element="#link-events-body"
                preserve-url
                :buffer="300"
            >
                <!--
                    The plain wrapper is load-bearing. Inertia finds its scroll
                    container by walking up from `items-element`, and it treats
                    any `overflow` element whose parent is a flex column as one
                    — which this inner div's would be. Nesting it inside a plain
                    block hides it from that walk, so the page above stays the
                    scroll container for loading, while the table keeps its
                    sideways scroll to itself.
                -->
                <div>
                    <div class="overflow-x-auto" data-testid="link-events-table">
                        <!-- The link column would repeat the one link this
                             screen is already about. -->
                        <EventsTable
                            :rows="table.data"
                            items-id="link-events-body"
                            :show-link="false"
                        />
                    </div>
                </div>
            </InfiniteScroll>
        </div>
    </AppLayout>
</template>
