<script setup lang="ts">
import { IconClick, IconQrcode } from "@tabler/icons-vue";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import date from "@/date";
import { browserIconUrl } from "@/lib/browsers";
import { countryFlagUrl, countryFor } from "@/lib/countries";
import { deviceIconUrl } from "@/lib/devices";
import { languageFlagUrl, languageLabel } from "@/lib/languages";
import { osIconUrl } from "@/lib/os";
import { computed } from "vue";

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

export type { EventData };

const props = withDefaults(
    defineProps<{
        rows: EventData[];
        /**
         * The id Inertia's InfiniteScroll appends the next page into. Two
         * tables on one screen would collide on a shared one.
         */
        itemsId: string;
        /** The link column is noise on a screen that is already one link. */
        showLink?: boolean;
    }>(),
    { showLink: true },
);

// Every column, always. The picker that used to hide most of them was one more
// thing to configure before the screen told you anything.
const ALL_COLUMNS = [
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

const COLUMNS = computed(() =>
    ALL_COLUMNS.filter((column) => props.showLink || column.key !== "link"),
);

// A scan and a click are the two things a link can receive. The icon carries it
// on its own — the word repeated down every row was a column of noise — and the
// tooltip names it for anyone who does not know the glyph yet.
const eventIcon = (event: string) => (event === "qr-scan" ? IconQrcode : IconClick);

const eventLabel = (event: string) =>
    event === "qr-scan" ? "QR code scan" : "Click";
</script>

<template>
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
                <TableBody :id="itemsId">
                    <TableRow v-for="event in rows" :key="event.id">
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

                        <TableCell v-if="showLink" class="whitespace-nowrap">
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
</template>
