<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { Link, useForm, Head } from "@inertiajs/vue3";
import {
    IconSearch,
    IconCopy,
    IconClick,
    IconPencil,
    IconDots,
    IconTrash,
    IconQrcode,
    IconLink,
    IconExternalLink,
} from "@tabler/icons-vue";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import AppLayout from "@/layouts/AppLayout.vue";
import ConfirmDeleteModal from "@/components/ConfirmDeleteModal.vue";
import EmptyState from "@/components/EmptyState.vue";
import Pagination from "@/components/Pagination.vue";
import Qrcode from "@/components/Qrcode.vue";
import CreateModal from "./Create.vue";
import LinkStatus from "@/components/links/LinkStatus.vue";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";

import * as linksRoute from "@/routes/links";
import * as websitesRoute from "@/routes/websites";

import { copyToClipboard } from "@/lib/utils";
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
const confirmDeleteModal = ref<InstanceType<typeof ConfirmDeleteModal> | null>(null);

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

    <AppLayout :title="title" :total="total">
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

        <ConfirmDeleteModal
            ref="confirmDeleteModal"
            description="Are you sure you want to delete this link?"
        />

        <div class="p-4 sm:p-6">
            <div v-if="hasData" class="space-y-2">
                <div
                    v-for="data in table.data"
                    :key="data.id"
                    class="group relative flex items-center gap-4 rounded-xl border border-border bg-card px-4 py-3.5 transition-colors hover:bg-accent/40"
                >
                    <!--
                        The row's own click target sits underneath everything
                        else. Nesting the action buttons inside an <a> would be
                        invalid markup, so the link is an overlay and the
                        controls simply stack above it.
                    -->
                    <Link
                        :href="linksRoute.edit.url(data.id)"
                        class="absolute inset-0 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        :aria-label="`Edit ${data.link}`"
                    />

                    <!-- Favicon -->
                    <div class="pointer-events-none relative hidden sm:flex shrink-0 h-9 w-9 items-center justify-center rounded-lg border border-border bg-background">
                        <img
                            v-if="!faviconFailed[data.id]"
                            :src="websitesRoute.favicon.url({ query: { url: data.url } })"
                            alt=""
                            class="h-5 w-5 rounded"
                            @error="faviconFailed[data.id] = true"
                        />
                        <IconLink v-else class="h-4 w-4 text-muted-foreground" />
                    </div>

                    <!-- Link info -->
                    <div class="pointer-events-none relative min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-sm font-medium text-foreground truncate">{{ data.link }}</span>

                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <button
                                        type="button"
                                        class="pointer-events-auto shrink-0 text-muted-foreground transition-colors hover:text-foreground"
                                        @click="copyToClipboard(data.link, 'Link copied')"
                                    >
                                        <IconCopy class="h-3.5 w-3.5" />
                                        <span class="sr-only">Copy short link</span>
                                    </button>
                                </TooltipTrigger>
                                <TooltipContent>Copy short link</TooltipContent>
                            </Tooltip>

                            <LinkStatus
                                class="pointer-events-auto"
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
                        </div>

                        <div class="flex items-center gap-1.5 min-w-0">
                            <IconExternalLink class="h-3 w-3 shrink-0 text-muted-foreground" />
                            <span class="text-xs text-muted-foreground truncate">{{ data.url }}</span>
                        </div>

                        <div v-if="data.tags.length > 0" class="flex items-center gap-1 mt-1.5 flex-wrap">
                            <Badge
                                v-for="tag in data.tags"
                                :key="tag.id"
                                variant="secondary"
                                class="h-4 gap-1 px-1.5 text-[10px] font-medium rounded"
                            >
                                <span
                                    v-if="tag.color"
                                    class="size-1.5 shrink-0 rounded-full"
                                    :style="{ backgroundColor: tag.color }"
                                />
                                {{ tag.name }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="relative flex shrink-0 items-center gap-2">
                        <!-- Created -->
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <span class="hidden text-xs text-muted-foreground lg:block">
                                    {{ date.diffForHumans(data.created_at) }}
                                </span>
                            </TooltipTrigger>
                            <TooltipContent>
                                Created {{ date.formatDateTime(data.created_at) }}
                            </TooltipContent>
                        </Tooltip>

                        <!-- Clicks -->
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <div class="flex items-center gap-1 text-xs text-muted-foreground bg-muted rounded-md px-2 py-1 font-medium tabular-nums">
                                    <IconClick class="h-3.5 w-3.5" />
                                    <span>{{ formatCount(data.clicks) }}</span>
                                </div>
                            </TooltipTrigger>
                            <TooltipContent>
                                {{ formatNumber(data.clicks) }} {{ data.clicks === 1 ? 'click' : 'clicks' }}
                                <template v-if="data.last_click">
                                    · last click {{ date.diffForHumans(data.last_click) }}
                                </template>
                            </TooltipContent>
                        </Tooltip>

                        <!--
                            Muted rather than hidden: `opacity-0 group-hover` put
                            these out of reach on any device without a pointer.
                        -->
                        <div class="hidden sm:flex items-center gap-1">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-7 w-7 text-muted-foreground group-hover:text-foreground"
                                        as-child
                                    >
                                        <Link :href="linksRoute.edit.url(data.id)">
                                            <IconPencil class="h-3.5 w-3.5" />
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
                                        class="h-7 w-7 text-muted-foreground group-hover:text-foreground"
                                        @click="qrcodeModal?.open(data)"
                                    >
                                        <IconQrcode class="h-3.5 w-3.5" />
                                        <span class="sr-only">QR code</span>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>QR code</TooltipContent>
                            </Tooltip>
                        </div>

                        <!-- Dots menu -->
                        <DropdownMenu>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon" class="h-7 w-7">
                                            <IconDots class="h-4 w-4 text-muted-foreground" />
                                            <span class="sr-only">More actions</span>
                                        </Button>
                                    </DropdownMenuTrigger>
                                </TooltipTrigger>
                                <TooltipContent>More actions</TooltipContent>
                            </Tooltip>
                            <DropdownMenuContent align="end" class="w-52">
                                <DropdownMenuItem as-child>
                                    <Link :href="linksRoute.edit.url(data.id)" class="flex items-center cursor-pointer">
                                        <IconPencil class="mr-2 h-4 w-4" />
                                        Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="qrcodeModal?.open(data)" class="cursor-pointer">
                                    <IconQrcode class="mr-2 h-4 w-4" />
                                    QR Code
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    @click="copyToClipboard(data.link, 'Link copied')"
                                    class="cursor-pointer"
                                >
                                    <IconCopy class="mr-2 h-4 w-4" />
                                    Copy Link
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    @click="copyToClipboard(data.id, 'Link ID copied')"
                                    class="cursor-pointer"
                                >
                                    <IconCopy class="mr-2 h-4 w-4" />
                                    Copy Link ID
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    @click="confirmDeleteModal?.open({ url: linksRoute.destroy.url(data.id) })"
                                    class="text-destructive focus:text-destructive cursor-pointer"
                                >
                                    <IconTrash class="mr-2 h-4 w-4" />
                                    Delete
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>

            <EmptyState
                v-if="!hasData"
                :icon="IconLink"
                title="No links yet"
                description="Create your first link and start tracking clicks"
            />
        </div>

        <template v-if="table.next_page_url" #pagination>
            <Pagination :data="table" />
        </template>
    </AppLayout>
</template>
