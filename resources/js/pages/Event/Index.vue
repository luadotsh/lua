<script setup lang="ts">
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { InfiniteScroll, router } from "@inertiajs/vue3";
import { IconClick, IconQrcode } from "@tabler/icons-vue";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import AppLayout from "@/layouts/AppLayout.vue";
import date from "@/date";
import { browserIconUrl } from "@/lib/browsers";
import { countryFlagUrl, countryFor } from "@/lib/countries";
import { deviceIconUrl } from "@/lib/devices";
import { languageFlagUrl, languageLabel } from "@/lib/languages";
import { osIconUrl } from "@/lib/os";

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

// A scan and a click are the two things a link can receive. The icon carries it
// on its own — the word repeated down every row was a column of noise — and the
// tooltip names it for anyone who does not know the glyph yet.
const eventIcon = (event: string) => (event === "qr-scan" ? IconQrcode : IconClick);

const eventLabel = (event: string) =>
    event === "qr-scan" ? "QR code scan" : "Click";

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
            <div class="min-h-0 min-w-0 flex-1 overflow-auto pb-px" data-testid="events-scroll">
                <InfiniteScroll
                    data="table"
                    items-element="#events-body"
                    preserve-url
                    :buffer="300"
                >
                <Table>
                    <TableHeader sticky>
                        <TableRow>
                            <TableHead
                                v-for="column in COLUMNS"
                                :key="column.key"
                                class="whitespace-nowrap"
                                :class="column.key === 'event' ? 'w-10 text-center' : ''"
                            >
                                {{ column.label }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody id="events-body">
                        <TableRow v-for="event in table.data" :key="event.id">
                            <TableCell class="w-10">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <span class="flex justify-center">
                                            <component
                                                :is="eventIcon(event.event)"
                                                class="size-4 text-muted-foreground"
                                            />
                                            <span class="sr-only">{{ eventLabel(event.event) }}</span>
                                        </span>
                                    </TooltipTrigger>
                                    <TooltipContent>{{ eventLabel(event.event) }}</TooltipContent>
                                </Tooltip>
                            </TableCell>

                            <TableCell class="whitespace-nowrap">
                                <a
                                    :href="event.link.link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:underline"
                                >
                                    {{ event.link.link }}
                                </a>
                            </TableCell>

                            <TableCell class="whitespace-nowrap">
                                <span v-if="event.country" class="flex items-center gap-1.5">
                                    <img
                                        :src="countryFlagUrl(event.country)"
                                        alt=""
                                        aria-hidden="true"
                                        class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                        loading="lazy"
                                        @error="(e) => ((e.target as HTMLImageElement).style.display = 'none')"
                                    />
                                    {{ countryFor(event.country).name }}
                                </span>
                            </TableCell>

                            <!-- A region and a city belong to the country on
                                 the same row, so they carry its flag too. -->
                            <TableCell class="whitespace-nowrap">
                                <span v-if="event.region" class="flex items-center gap-1.5">
                                    <img
                                        v-if="event.country"
                                        :src="countryFlagUrl(event.country)"
                                        alt=""
                                        aria-hidden="true"
                                        class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                        loading="lazy"
                                        @error="(e) => ((e.target as HTMLImageElement).style.display = 'none')"
                                    />
                                    {{ event.region }}
                                </span>
                            </TableCell>

                            <TableCell class="whitespace-nowrap">
                                <span v-if="event.city" class="flex items-center gap-1.5">
                                    <img
                                        v-if="event.country"
                                        :src="countryFlagUrl(event.country)"
                                        alt=""
                                        aria-hidden="true"
                                        class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                        loading="lazy"
                                        @error="(e) => ((e.target as HTMLImageElement).style.display = 'none')"
                                    />
                                    {{ event.city }}
                                </span>
                            </TableCell>

                            <TableCell class="whitespace-nowrap">
                                <span class="flex items-center gap-1.5">
                                    <img
                                        v-if="deviceIconUrl(event.device)"
                                        :src="deviceIconUrl(event.device) ?? ''"
                                        alt=""
                                        aria-hidden="true"
                                        class="size-4 shrink-0"
                                        loading="lazy"
                                        @error="(e) => ((e.target as HTMLImageElement).style.display = 'none')"
                                    />
                                    {{ event.device }}
                                </span>
                            </TableCell>

                            <TableCell class="whitespace-nowrap">
                                <span class="flex items-center gap-1.5">
                                    <img
                                        :src="browserIconUrl(event.browser)"
                                        alt=""
                                        aria-hidden="true"
                                        class="size-4 shrink-0"
                                        loading="lazy"
                                    />
                                    {{ event.browser }}
                                </span>
                            </TableCell>

                            <TableCell class="whitespace-nowrap">
                                <span class="flex items-center gap-1.5">
                                    <img
                                        :src="osIconUrl(event.os)"
                                        alt=""
                                        aria-hidden="true"
                                        class="size-4 shrink-0"
                                        loading="lazy"
                                    />
                                    {{ event.os }}
                                </span>
                            </TableCell>
                            <TableCell class="whitespace-nowrap">
                                {{ date.formatDateTime(event.created_at) }}
                            </TableCell>
                            <TableCell class="whitespace-nowrap">
                                <span v-if="event.language" class="flex items-center gap-1.5">
                                    <img
                                        v-if="languageFlagUrl(event.language)"
                                        :src="languageFlagUrl(event.language) ?? ''"
                                        alt=""
                                        aria-hidden="true"
                                        class="h-3 w-[18px] shrink-0 rounded-[2px] object-cover"
                                        loading="lazy"
                                        @error="(e) => ((e.target as HTMLImageElement).style.display = 'none')"
                                    />
                                    {{ languageLabel(event.language) }}
                                </span>
                            </TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_source }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_medium }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_campaign }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_content }}</TableCell>
                            <TableCell class="whitespace-nowrap">{{ event.utm_term }}</TableCell>
                            <TableCell class="max-w-xs truncate">{{ event.referer }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                </InfiniteScroll>
            </div>
        </div>
    </AppLayout>
</template>
