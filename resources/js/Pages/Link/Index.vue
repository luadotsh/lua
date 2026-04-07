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

import * as linksRoute from "@/routes/links";
import * as websitesRoute from "@/routes/websites";

import { copyToClipboard, kFormatter } from "@/lib/utils";
import date from "@/date";
import debounce from "@/debounce";

import CreateModal from "./Create.vue";
import Edit from "./Edit.vue";

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
}

interface Table {
    data: LinkData[];
    total: number;
    next_page_url: string | null;
}

const props = defineProps<{
    table: Table;
    hasData: boolean;
    link?: LinkData | null;
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

const title = computed(() => {
    if (searchForm.q) {
        return `Search results for "${searchForm.q}"`;
    }
    if (props.table.total <= 1) {
        return `Links`;
    }
    return `Links (${props.table.total})`;
});

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
    <Edit v-if="link" :link="link" />
    <Qrcode ref="qrcodeModal" />

    <AppLayout>
        <template #header-right>
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
                    class="group flex items-center gap-4 rounded-xl border border-border bg-card px-4 py-3.5 transition-colors hover:bg-accent/40"
                >
                    <!-- Favicon -->
                    <div class="hidden sm:flex shrink-0 h-9 w-9 items-center justify-center rounded-lg border border-border bg-background">
                        <img
                            :src="websitesRoute.favicon.url({ query: { url: data.url } })"
                            alt=""
                            class="h-5 w-5 rounded"
                            @error="($event.target as HTMLImageElement).style.display = 'none'"
                        />
                    </div>

                    <!-- Link info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-sm font-medium text-foreground truncate">{{ data.link }}</span>
                            <button
                                class="opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
                                @click="copyToClipboard(data.link, 'Link copied')"
                            >
                                <IconCopy class="h-3.5 w-3.5 text-muted-foreground hover:text-foreground transition-colors" />
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="text-xs text-muted-foreground truncate">{{ data.url }}</span>
                        </div>
                        <div v-if="data.tags.length > 0" class="flex items-center gap-1 mt-1.5 flex-wrap">
                            <Badge
                                v-for="tag in data.tags"
                                :key="tag.id"
                                variant="secondary"
                                class="h-4 px-1.5 text-[10px] font-medium rounded"
                            >
                                {{ tag.name }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex shrink-0 items-center gap-2">
                        <!-- Clicks -->
                        <div class="flex items-center gap-1 text-xs text-muted-foreground bg-muted rounded-md px-2 py-1 font-medium tabular-nums">
                            <IconClick class="h-3.5 w-3.5" />
                            <span>{{ kFormatter(data.clicks) }}</span>
                        </div>

                        <!-- Inline actions (hover) -->
                        <div class="hidden sm:flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <Link :href="linksRoute.index.url(data.id)">
                                <Button variant="ghost" size="icon" class="h-7 w-7">
                                    <IconPencil class="h-3.5 w-3.5" />
                                </Button>
                            </Link>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-7 w-7"
                                @click="qrcodeModal?.open(data)"
                            >
                                <IconQrcode class="h-3.5 w-3.5" />
                            </Button>
                        </div>

                        <!-- Dots menu -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-7 w-7">
                                    <IconDots class="h-4 w-4 text-muted-foreground" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-52">
                                <DropdownMenuItem as-child>
                                    <Link :href="linksRoute.index.url(data.id)" class="flex items-center cursor-pointer">
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
