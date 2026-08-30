<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { Head, InfiniteScroll, Link, useForm } from "@inertiajs/vue3";
import {
    IconSearch,
    IconCopy,
    IconClick,
    IconPencil,
    IconQrcode,
    IconLink,
    IconExternalLink,
} from "@tabler/icons-vue";

import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import AppLayout from "@/layouts/AppLayout.vue";
import EmptyState from "@/components/EmptyState.vue";
import Qrcode from "@/components/Qrcode.vue";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import CreateModal from "./Create.vue";
import LinkStatus from "@/components/links/LinkStatus.vue";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";

import * as linksRoute from "@/routes/links";

import { copyToClipboard, favicon } from "@/lib/utils";
import { formatCount, formatNumber } from "@/lib/metrics";
import date from "@/date";
import debounce from "@/debounce";


interface Tag {
    id: string | number;
    name: string;
    color?: string;
}

interface LinkData {
    id: string;
    link: string;
    url: string;
    domain: string;
    key: string;
    tags: Tag[];
    clicks: number;
    last_click: string | null;
    created_at: string;
    has_password: boolean;
    expires_at: string | null;
    ios: string | null;
    android: string | null;
    utm_source: string | null;
    utm_medium: string | null;
    utm_campaign: string | null;
    utm_term: string | null;
    utm_content: string | null;
}

interface Table {
    data: LinkData[];
    total: number;
    next_page_url: string | null;
}

// A favicon that 404s used to be hidden outright, leaving an empty bordered
// box; the row falls back to a glyph instead.
const faviconFailed = ref<Record<string, boolean>>({});

const props = defineProps<{
    table: Table;
    hasData: boolean;
}>();

const qrcodeModal = ref<InstanceType<typeof Qrcode> | null>(null);
const createModal = ref<InstanceType<typeof CreateModal> | null>(null);

const searchForm = useForm({
    q: "",
});

const searchDebounce = debounce(function () {
    searchForm.get(linksRoute.index.url(), {
        preserveScroll: true,
        preserveState: true,
    });
}, 300);

const title = computed(() =>
    searchForm.q ? `Search results for "${searchForm.q}"` : "Links",
);

// The count belongs to the list, not to a search — "Search results (3)" would be
// counting the page, and the header already says what was searched for.
const total = computed(() => (searchForm.q ? null : props.table.total));

onMounted(() => {
    const url = new URL(window.location.href);
    const q = url.searchParams.get("q");
    if (q) {
        searchForm.q = q;
    }
});
</script>

<template>
    <Head title="Links" />

    <CreateModal ref="createModal" />
    <Qrcode ref="qrcodeModal" />

    <AppLayout :title="title" :total="total" full-width>
        <template #header-actions>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none" />
                    <Input
                        class="pl-9 w-64"
                        autocomplete="off"
                        type="text"
                        placeholder="Search links..."
                        v-model="searchForm.q"
                        @keyup="searchDebounce"
                    />
                </div>
                <Button @click="createModal?.open()">New Link</Button>
            </div>
        </template>

        <div class="flex min-h-0 flex-1 flex-col">
            <template v-if="hasData">
                <!-- The table owns its scrolling, both ways: that keeps the
                     header row above it still, and Inertia finds the scroll
                     container by walking up from `items-element`, so it has to
                     be one that scrolls vertically. -->
                <InfiniteScroll
                    data="table"
                    items-element="#links-body"
                    preserve-url
                    :buffer="300"
                >
                        <Table>
                            <TableHeader sticky>
                                <!--
                                    `w-px` on a cell in an auto-layout table is
                                    read as "as narrow as the content allows",
                                    so every column shrinks to fit and the one
                                    marked `w-full` — the destination, the only
                                    one that benefits from room — absorbs what
                                    is left. Without this the browser split the
                                    slack evenly and Tags sat in a wide empty
                                    gap.
                                -->
                                <TableRow>
                                    <TableHead class="w-px whitespace-nowrap">Short link</TableHead>
                                    <TableHead class="w-full whitespace-nowrap">Destination</TableHead>
                                    <TableHead class="w-px whitespace-nowrap">Tags</TableHead>
                                    <TableHead class="w-px whitespace-nowrap text-right">Clicks</TableHead>
                                    <TableHead class="w-px whitespace-nowrap">Created</TableHead>
                                    <TableHead class="w-px"><span class="sr-only">Actions</span></TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody id="links-body">
                                <TableRow v-for="data in table.data" :key="data.id" class="group">
                                    <TableCell class="w-px whitespace-nowrap">
                                        <span class="flex items-center gap-2">
                                            <img
                                                v-if="!faviconFailed[data.id]"
                                                :src="favicon(data.url)"
                                                alt=""
                                                aria-hidden="true"
                                                class="size-4 shrink-0 rounded-sm"
                                                loading="lazy"
                                                @error="faviconFailed[data.id] = true"
                                            />
                                            <IconLink v-else class="size-4 shrink-0 text-muted-foreground" />

                                            <Link
                                                :href="linksRoute.edit.url(data.id)"
                                                class="font-medium hover:underline"
                                            >
                                                {{ data.link }}
                                            </Link>

                                            <Tooltip>
                                                <TooltipTrigger as-child>
                                                    <button
                                                        type="button"
                                                        class="text-muted-foreground transition-colors hover:text-foreground"
                                                        @click="copyToClipboard(data.link, 'Link copied')"
                                                    >
                                                        <IconCopy class="size-3.5" />
                                                        <span class="sr-only">Copy short link</span>
                                                    </button>
                                                </TooltipTrigger>
                                                <TooltipContent>Copy short link</TooltipContent>
                                            </Tooltip>

                                            <LinkStatus
                                                :has-password="data.has_password"
                                                :expires-at="data.expires_at"
                                                :ios="data.ios"
                                                :android="data.android"
                                                :utm-source="data.utm_source"
                                                :utm-medium="data.utm_medium"
                                                :utm-campaign="data.utm_campaign"
                                                :utm-term="data.utm_term"
                                                :utm-content="data.utm_content"
                                            />
                                        </span>
                                    </TableCell>

                                    <TableCell class="w-full max-w-0">
                                        <a
                                            :href="data.url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="flex items-center gap-1.5 text-muted-foreground hover:text-foreground hover:underline"
                                        >
                                            <IconExternalLink class="size-3 shrink-0" />
                                            <span class="truncate">{{ data.url }}</span>
                                        </a>
                                    </TableCell>

                                    <TableCell class="w-px whitespace-nowrap">
                                        <span v-if="data.tags.length" class="flex items-center gap-1">
                                            <Badge
                                                v-for="tag in data.tags"
                                                :key="tag.id"
                                                variant="secondary"
                                                class="h-5 gap-1 rounded px-1.5 text-[11px] font-medium"
                                            >
                                                <span
                                                    v-if="tag.color"
                                                    class="size-1.5 shrink-0 rounded-full"
                                                    :style="{ backgroundColor: tag.color }"
                                                />
                                                {{ tag.name }}
                                            </Badge>
                                        </span>
                                    </TableCell>

                                    <TableCell class="w-px text-right whitespace-nowrap tabular-nums">
                                        <Tooltip>
                                            <TooltipTrigger as-child>
                                                <span class="inline-flex items-center gap-1">
                                                    <IconClick class="size-3.5 text-muted-foreground" />
                                                    {{ formatCount(data.clicks) }}
                                                </span>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                {{ formatNumber(data.clicks) }}
                                                {{ data.clicks === 1 ? 'click' : 'clicks' }}
                                                <template v-if="data.last_click">
                                                    · last click {{ date.diffForHumans(data.last_click) }}
                                                </template>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TableCell>

                                    <TableCell class="w-px whitespace-nowrap text-muted-foreground">
                                        <Tooltip>
                                            <TooltipTrigger as-child>
                                                <span>{{ date.diffForHumans(data.created_at) }}</span>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                Created {{ date.formatDateTime(data.created_at) }}
                                            </TooltipContent>
                                        </Tooltip>
                                    </TableCell>

                                    <TableCell class="w-px">
                                        <div class="flex items-center justify-end gap-1">
                                            <Tooltip>
                                                <TooltipTrigger as-child>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7 text-muted-foreground group-hover:text-foreground"
                                                        as-child
                                                    >
                                                        <Link :href="linksRoute.edit.url(data.id)">
                                                            <IconPencil class="size-3.5" />
                                                            <span class="sr-only">Edit link</span>
                                                        </Link>
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>Edit link</TooltipContent>
                                            </Tooltip>

                                            <Tooltip>
                                                <TooltipTrigger as-child>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7 text-muted-foreground group-hover:text-foreground"
                                                        @click="qrcodeModal?.open(data)"
                                                    >
                                                        <IconQrcode class="size-3.5" />
                                                        <span class="sr-only">QR code</span>
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>QR code</TooltipContent>
                                            </Tooltip>

                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                </InfiniteScroll>
            </template>

            <div v-else class="p-4 sm:p-6">
                <EmptyState
                    :icon="IconLink"
                    title="No links yet"
                    description="Create your first link and start tracking clicks"
                />
            </div>
        </div>
    </AppLayout>
</template>
